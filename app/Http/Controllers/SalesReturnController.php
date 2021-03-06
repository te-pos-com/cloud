<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesReturn;
use App\SalesReturnItem;
use App\SalesReturnItemTax;
use App\Transaction;
use App\Stock;
use App\Tax;
use App\Hpp;
use Validator;
use DataTables;
use DB;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.accounting.sales_return.list');
    }

	public function get_table_data() {

        $currency = currency();

        $salesReturns = SalesReturn::with('customer')
									  ->select('sales_return.*')
									  ->orderBy("id", "desc");

        return Datatables::eloquent($salesReturns)
            ->editColumn('grand_total', function ($salesReturn) use ($currency) {
                return "<span class='float-right'>" . decimalPlace($salesReturn->grand_total, $currency) . "</span>";
            })
			->editColumn('paid', function ($purchaseReturn) use ($currency) {
                return '<span class="float-right">' . decimalPlace($purchaseReturn->paid, $currency) . '</span>';
            })
            ->editColumn('payment_status', function ($purchaseReturn) {
                if ($purchaseReturn->payment_status == 0) {
                    return '<span class="badge badge-danger">' . _lang('Blum Lunas') . '</span>';
                } else {
                    return '<span class="badge badge-success">' . _lang('Lunas') . '</span>';
                }
            })
            ->addColumn('action', function ($salesReturn) {
				if ($salesReturn->payment_status==1){
					return '<div class="dropdown text-center">'
					. '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
					. '<i class="mdi mdi-chevron-down"></i></button>'
					. '<div class="dropdown-menu">'
					. '<a class="dropdown-item" href="' . action('SalesReturnController@show', $salesReturn->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
					. '<a href="' . route('sales_return.view_payment', $salesReturn->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
					. '</div>'
					. '</div>';
				}
				else{
					return '<div class="dropdown text-center">'
					. '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
					. '<i class="mdi mdi-chevron-down"></i></button>'
					. '<div class="dropdown-menu">'
					. '<a class="dropdown-item" href="' . action('SalesReturnController@edit', $salesReturn->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a></li>'
					. '<a class="dropdown-item" href="' . action('SalesReturnController@show', $salesReturn->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
					. '<a href="' . route('sales_return.create_payment', $salesReturn->id) . '" data-title="' . _lang('Make Payment') . '" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('Make Payment') . '</a>'
					. '<a href="' . route('sales_return.view_payment', $salesReturn->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
					. '<form action="' . action('SalesReturnController@destroy', $salesReturn->id) . '" method="post">'
					. csrf_field()
					. '<input name="_method" type="hidden" value="DELETE">'
					. '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
					. '</form>'
					. '</div>'
					. '</div>';
				}
            })
            ->setRowId(function ($salesReturn) {
                return "row_" . $salesReturn->id;
            })
            ->rawColumns(['grand_total', 'action', 'paid', 'payment_status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.sales_return.create');
		}else{
           return view('backend.accounting.sales_return.modal.create');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'return_date' => 'required',
			'customer_id' => 'required',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
			'product_id'     => 'required',
        ], [
            'product_id.required' => _lang('You must select at least one product or service'),
        ]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('sales_returns.create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		DB::beginTransaction();
			
		$attachemnt = "";
	    if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}
		
        $salesReturn                = new SalesReturn();
	    $salesReturn->return_date   = $request->input('return_date');
	    $salesReturn->invoice_number= $request->input('invoice_number');
        $salesReturn->cabang_id     = $request->input('cabang_id');
		$salesReturn->customer_id   = $request->input('customer_id');
		$salesReturn->tax_amount    = $request->tax_total;
		$salesReturn->product_total = $request->input('product_total');
		$salesReturn->grand_total   = ($salesReturn->product_total + $salesReturn->tax_amount);
		$salesReturn->attachemnt    = $attachemnt;
		$salesReturn->note          = $request->input('note');
	
		$salesReturn->save();
		
		$taxes = Tax::all();

		//Save Sales Return item
		for($i = 0; $i < count($request->product_id); $i++ ){
			$salesReturnItem                    = new SalesReturnItem();
			$salesReturnItem->sales_return_id   = $salesReturn->id;
			$salesReturnItem->product_id        = $request->product_id[$i];
			$salesReturnItem->gudang_id         = $request->gudang_id[$i];
			$salesReturnItem->description       = "";
			$salesReturnItem->quantity          = $request->quantity[$i];
			$salesReturnItem->unit_cost         = $request->unit_cost[$i];
			$salesReturnItem->discount          = $request->discount[$i];
			$salesReturnItem->tax_amount        = $request->product_tax[$i];
			$salesReturnItem->sub_total         = $request->sub_total[$i];
			$salesReturnItem->save();
			
			
            $hpp                        = new Hpp();
            $hpp->transaksi_id          = $salesReturn->id;
            $hpp->transaksi_item_id     = $salesReturnItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 2;
            $hpp->stok                  = $hpp->stok + $salesReturnItem->quantity;
            $hpp->stok_sisa             = $hpp->stok-$hpp->stok_terpakai; 
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();

			
			
			//Store Sales Return Taxes
			if(isset($request->tax[$salesReturnItem->product_id])){
				foreach($request->tax[$salesReturnItem->product_id] as $taxId){
					$tax = $taxes->firstWhere('id', $taxId);
					
					$salesReturnItemTax = new SalesReturnItemTax();
					$salesReturnItemTax->sales_return_id = $salesReturnItem->sales_return_id;
					$salesReturnItemTax->sales_return_item_id = $salesReturnItem->id;
					$salesReturnItemTax->tax_id = $tax->id;
					$tax_type = $tax->type == 'percent' ? '%' : '';
					$salesReturnItemTax->name = $tax->tax_name.' @ '.$tax->rate.$tax_type;
					$salesReturnItemTax->amount = $tax->type == 'percent' ? ($salesReturnItem->sub_total / 100) * $tax->rate : $tax->rate;
					$salesReturnItemTax->save();
				}
			}

			//Update Stock
			$stock = Stock::where("product_id", $salesReturnItem->product_id)->first();
			$stock->quantity = $stock->quantity + $salesReturnItem->quantity;
			$stock->save();
		}
		
		DB::commit();

        
		if(! $request->ajax()){
           return redirect()->route('sales_returns.show', $salesReturn->id)->with('success', _lang('Sales Returned Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Sales Returned Sucessfully'),'data'=>$purchase]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $sales_return = SalesReturn::find($id);
		$sales_return_taxes = SalesReturnItemTax::where('sales_return_id',$id)
												->selectRaw('sales_return_item_taxes.*,sum(sales_return_item_taxes.amount) as tax_amount')
												->groupBy('sales_return_item_taxes.tax_id')
												->get();
		
		return view('backend.accounting.sales_return.view',compact('sales_return','sales_return_taxes','id'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $sales = SalesReturn::find($id);
		if(! $request->ajax()){
		   return view('backend.accounting.sales_return.edit',compact('sales','id'));
		}else{
           return view('backend.accounting.sales_return.modal.edit',compact('sales','id'));
		}  
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'return_date'   => 'required',
			'customer_id'   => 'required',
			'cabang_id'     => 'required',
            'invoice_number'=> 'required',
			'sub_total.*'   => 'required|numeric',
			'attachemnt'    => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
			'product_id'    => 'required',
        ], [
            'product_id.required' => _lang('You must select at least one product or service'),
        ]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('sales_returns.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}

		DB::beginTransaction();
		
			
		$attachemnt = "";
	    if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}
		
		DB::select("ALTER TABLE sales_return AUTO_INCREMENT=0");
		DB::select("ALTER TABLE sales_return_items AUTO_INCREMENT=0");
		DB::select("ALTER TABLE sales_return_item_taxes AUTO_INCREMENT=0");
		
        $salesReturn                    = SalesReturn::find($id);
	    $previous_amount                = $salesReturn->grand_total;
		$salesReturn->return_date       = $request->input('return_date');
		$salesReturn->cabang_id         = $request->input('cabang_id');
		$salesReturn->customer_id       = $request->input('customer_id');
		$salesReturn->tax_amount        = $request->tax_total;
		$salesReturn->product_total     = $request->input('product_total');
		$salesReturn->grand_total       = ($salesReturn->product_total + $salesReturn->tax_amount);
		$salesReturn->attachemnt        = $attachemnt;
		$salesReturn->note              = $request->input('note');
	
		$salesReturn->save();
		
		$taxes = Tax::all();


		//Remove Previous Purcahse item
		$previous_items = SalesReturnItem::where("sales_return_id",$id)->get();
		foreach($previous_items as $p_item){
			$returnItem = SalesReturnItem::find($p_item->id);
			
			$hpp = Hpp::where("transaksi_item_id", $p_item->id)->where("invoice_number",$salesReturn->invoice_number)->first();
            if ($hpp){
                $hpp->delete();
            }
			
			$returnItem->delete();
			update_stock($p_item->product_id);
		}
		
		$salesReturnItemTax = SalesReturnItemTax::where("sales_return_id",$id);
		$salesReturnItemTax->delete();

		for( $i = 0; $i < count($request->product_id); $i++ ){
			$returnItem                  = new SalesReturnItem();
			$returnItem->sales_return_id = $salesReturn->id;
			$returnItem->product_id      = $request->product_id[$i];
			$returnItem->gudang_id       = $request->gudang_id[$i];
			$returnItem->description     = "";
			$returnItem->quantity        = $request->quantity[$i];
			$returnItem->unit_cost       = $request->unit_cost[$i];
			$returnItem->discount        = $request->discount[$i];
			$returnItem->tax_amount      = $request->product_tax[$i];
			$returnItem->sub_total       = $request->sub_total[$i];
			$returnItem->save();
			
			
			$hpp                        = new Hpp();
            $hpp->transaksi_id          = $salesReturn->id;
            $hpp->transaksi_item_id     = $returnItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $salesReturn->invoice_number;
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 2;
            $hpp->stok                  = $hpp->stok + $returnItem->quantity;
            $hpp->stok_sisa             = $hpp->stok-$hpp->stok_terpakai; 
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();
                
			
			//Store Sales Return Taxes
			if(isset($request->tax[$returnItem->product_id])){
				foreach($request->tax[$returnItem->product_id] as $taxId){
					$tax = $taxes->firstWhere('id', $taxId);
					
					$salesReturnItemTax = new SalesReturnItemTax();
					$salesReturnItemTax->sales_return_id = $returnItem->sales_return_id;
					$salesReturnItemTax->sales_return_item_id = $returnItem->id;
					$salesReturnItemTax->tax_id = $tax->id;
					$tax_type = $tax->type == 'percent' ? '%' : '';
					$salesReturnItemTax->name = $tax->tax_name.' @ '.$tax->rate.$tax_type;
					$salesReturnItemTax->amount = $tax->type == 'percent' ? ($returnItem->sub_total / 100) * $tax->rate : $tax->rate;
					$salesReturnItemTax->save();
				}
			}

			update_stock($request->product_id[$i]);

		}
		
		DB::commit();

				
		if(! $request->ajax()){
           return redirect()->route('sales_returns.show', $salesReturn->id)->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$purchase]);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		DB::beginTransaction();
		
        $salesReturn = SalesReturn::find($id);
		$salesReturn->delete();
		
		//Remove Sales Return Items
		$salesReturnItems = SalesReturnItem::where("sales_return_id",$id)->get();
		foreach($salesReturnItems as $p_item){
			$returnItem = SalesReturnItem::find($p_item->id);
			
			$hpp = Hpp::where("transaksi_item_id", $p_item->id)->where("invoice_number",$salesReturn->invoice_number)->first();
            if ($hpp){
                $hpp->delete();
            }
			$returnItem->delete();
			update_stock($p_item->product_id);
		}
		
		$salesReturnItemTax = SalesReturnItemTax::where('sales_return_id',$id);
		$salesReturnItemTax->delete();
		
		DB::commit();

        return back()->with('success',_lang('Deleted Sucessfully'));
	}
	
	public function store_payment(Request $request, $id = '') {
        if ($request->isMethod('get')) {
            $sales_return = SalesReturn::find($id);

            if ($request->ajax()) {
                return view('backend.accounting.sales_return.modal.create_payment', compact('sales_return', 'id'));
            }
        }
        if(jenis_langganan()=="POS" || jenis_langganan()=="TRADING"){
            $validator = Validator::make($request->all(), [
                'sales_return_id'       => 'required',
                'amount'            => 'required|numeric',
                'payment_method_id' => 'required',
                'reference'         => 'nullable|max:50',
                'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            ]);    
        }
        else{
            $validator = Validator::make($request->all(), [
                'sales_return_id'       => 'required',
                'account_id'        	   => 'required',
                'chart_id'          	   => 'required',
                'amount'                   => 'required|numeric',
                'payment_method_id'        => 'required',
                'reference'                => 'nullable|max:50',
                'attachment'        	   => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            ]);    
        }
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = "";
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/transactions/", $attachment);
        }
		DB::select("ALTER TABLE transactions AUTO_INCREMENT=0");

        if(jenis_langganan()=="POS"||jenis_langganan()=="TRADING"){
            $transaction                    = new Transaction();
            $transaction->trans_date        = date('Y-m-d');
            $transaction->type              = 'expense';
            $transaction->dr_cr             = 'dr';
            $transaction->amount            = $request->input('amount');
            $transaction->payment_method_id = $request->input('payment_method_id');
            $transaction->sales_return_id       = $request->input('sales_return_id');
            $transaction->reference         = $request->input('reference');
            $transaction->note              = $request->input('note');
            $transaction->attachment        = $attachment;
            $transaction->user_id           = user_id();
        }
        else{
            $transaction                    = new Transaction();
            $transaction->trans_date        = date('Y-m-d');
            $transaction->account_id        = $request->input('account_id');
            $transaction->chart_id          = $request->input('chart_id');
            $transaction->type              = 'expense';
            $transaction->dr_cr             = 'dr';
            $transaction->amount            = $request->input('amount');
            $transaction->payment_method_id = $request->input('payment_method_id');
            $transaction->sales_return_id       = $request->input('sales_return_id');
            $transaction->reference         = $request->input('reference');
            $transaction->note              = $request->input('note');
            $transaction->attachment        = $attachment;
            $transaction->user_id           = user_id();
        }
        $transaction->user_id        = user_id();
        $transaction->save();

        //Update Sales Order Table
        $sales_return       = SalesReturn::find($transaction->sales_return_id);
        $sales_return->paid = $sales_return->paid + $transaction->amount;
        if (round($sales_return->paid, 2) >= $sales_return->grand_total) {
            $sales_return->payment_status = 1;
        }
        $sales_return->save();

        if ($request->ajax()) {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment was made Sucessfully'), 'data' => $transaction]);
        }
    }

	public function view_payment(Request $request, $sales_return_id) {

        $transactions = Transaction::where("sales_return_id", $sales_return_id)->get();
        if (!$request->ajax()) {
            return view('backend.accounting.sales_return.view_payment', compact('transactions'));
        } else {
            return view('backend.accounting.sales_return.modal.view_payment', compact('transactions'));
        }
    }

	
}