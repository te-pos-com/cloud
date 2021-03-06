<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseReturn;
use App\PurchaseReturnItem;
use App\PurchaseReturnItemTax;
use App\Transaction;
use App\Stock;
use App\Tax;
use App\Hpp;
use Validator;
use DataTables;
use DB;

class PurchaseReturnController extends Controller
{	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.accounting.purchase_return.list');
    }

	public function get_table_data() {

        $currency = currency();

        $purchaseReturns = PurchaseReturn::with('supplier')
										->select('purchase_return.*')
										->orderBy("id", "desc");

        return Datatables::eloquent($purchaseReturns)
            ->editColumn('grand_total', function ($purchaseReturn) use ($currency) {
                return "<span class='float-right'>" . decimalPlace($purchaseReturn->grand_total, $currency) . "</span>";
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
            ->addColumn('action', function ($purchaseReturn) {
				if ($purchaseReturn->payment_status==1){
					return '<div class="dropdown text-center">'
					. '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
					. '<i class="mdi mdi-chevron-down"></i></button>'
					. '<div class="dropdown-menu">'
					. '<a class="dropdown-item" href="' . action('PurchaseReturnController@show', $purchaseReturn->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
					. '<a href="' . route('purchase_return.view_payment', $purchaseReturn->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
					. '</div>'
                    . '</div>';
				}
				else{
					return '<div class="dropdown text-center">'
					. '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
					. '<i class="mdi mdi-chevron-down"></i></button>'
					. '<div class="dropdown-menu">'
					. '<a class="dropdown-item" href="' . action('PurchaseReturnController@edit', $purchaseReturn->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a></li>'
					. '<a class="dropdown-item" href="' . action('PurchaseReturnController@show', $purchaseReturn->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
					. '<a href="' . route('purchase_return.create_payment', $purchaseReturn->id) . '" data-title="' . _lang('Make Payment') . '" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('Make Payment') . '</a>'
					. '<a href="' . route('purchase_return.view_payment', $purchaseReturn->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
					. '<form action="' . action('PurchaseReturnController@destroy', $purchaseReturn->id) . '" method="post">'
					. csrf_field()
					. '<input name="_method" type="hidden" value="DELETE">'
					. '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
				}
            })
            ->setRowId(function ($purchaseReturn) {
                return "row_" . $purchaseReturn->id;
            })
            ->rawColumns(['grand_total', 'paid', 'payment_status', 'action'])
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
		   return view('backend.accounting.purchase_return.create');
		}else{
           return view('backend.accounting.purchase_return.modal.create');
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
			'return_date'   => 'required',
			'cabang_id'     => 'required',
			'supplier_id'   => 'required',
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
				return redirect()->route('purchase_returns.create')
							->withErrors($validator)
							->withInput();
			}			
		}

		DB::beginTransaction();
			
		$attachemnt = '';
	    if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}
		

        $purchaseReturn                     = new PurchaseReturn();
	    $purchaseReturn->return_date        = $request->input('return_date');
		$purchaseReturn->supplier_id        = $request->input('supplier_id');
		$purchaseReturn->cabang_id          = $request->input('cabang_id');
		$purchaseReturn->invoice_number     = $request->input('invoice_number');
		$purchaseReturn->account_id         = "";
		$purchaseReturn->chart_id           = "";
		$purchaseReturn->payment_method_id  = "";
		$purchaseReturn->tax_amount         = $request->tax_total;
		$purchaseReturn->product_total      = $request->input('product_total');
		$purchaseReturn->grand_total        = $purchaseReturn->product_total + $purchaseReturn->tax_amount;
		$purchaseReturn->attachemnt         = $attachemnt;
		$purchaseReturn->note               = $request->input('note');
        $purchaseReturn->user_id            = user_id();
	
		$purchaseReturn->save();
		
		$taxes = Tax::all();

		//Save Purcahse Return item
		for($i = 0; $i < count($request->product_id); $i++ ){
			$purchaseReturnItem                     = new PurchaseReturnItem();
			$purchaseReturnItem->purchase_return_id = $purchaseReturn->id;
			$purchaseReturnItem->product_id         = $request->product_id[$i];
			$purchaseReturnItem->gudang_id          = $request->gudang_id[$i];
			$purchaseReturnItem->description        = "";
			$purchaseReturnItem->quantity           = $request->quantity[$i];
			$purchaseReturnItem->unit_cost          = $request->unit_cost[$i];
			$purchaseReturnItem->discount           = $request->discount[$i];
			$purchaseReturnItem->tax_amount         = $request->product_tax[$i];
			$purchaseReturnItem->sub_total          = $request->sub_total[$i];
			
			$hpp_old = Hpp::where("stok_sisa",">=",$request->quantity[$i])
            ->where("item_id",$request->product_id[$i])->where("gudang_id",$request->gudang_id[$i])
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai + $purchaseReturnItem->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
			$purchaseReturnItem->save();
			
			
            $hpp                        = new Hpp();
            $hpp->transaksi_id          = $purchaseReturn->id;
            $hpp->transaksi_item_id     = $purchaseReturnItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 7;
            $hpp->stok                  = $hpp->stok + $purchaseReturnItem->quantity;
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();

			
			//Store Purchase Return Taxes
			if(isset($request->tax[$purchaseReturnItem->product_id])){
				foreach($request->tax[$purchaseReturnItem->product_id] as $taxId){
					$tax = $taxes->firstWhere('id', $taxId);
					
					$purchaseReturnItemTax = new PurchaseReturnItemTax();
					$purchaseReturnItemTax->purchase_return_id = $purchaseReturnItem->purchase_return_id;
					$purchaseReturnItemTax->purchase_return_item_id = $purchaseReturnItem->id;
					$purchaseReturnItemTax->tax_id = $tax->id;
					$tax_type = $tax->type == 'percent' ? '%' : '';
					$purchaseReturnItemTax->name = $tax->tax_name.' @ '.$tax->rate.$tax_type;
					$purchaseReturnItemTax->amount = $tax->type == 'percent' ? ($purchaseReturnItem->sub_total / 100) * $tax->rate : $tax->rate;
					$purchaseReturnItemTax->save();
				}
			}

			//Update Stock
			$stock = Stock::where("product_id", $purchaseReturnItem->product_id)->first();
			$stock->quantity = $stock->quantity - $purchaseReturnItem->quantity;
			$stock->save();
		}

		//Credit Account
		/*
		$transaction = new Transaction();
	    $transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = $purchaseReturn->grand_total;
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->purchase_return_id = $purchaseReturn->id;
		$transaction->note = $request->input('note');
        $transaction->save();
		*/
		DB::commit();

        return redirect()->route('purchase_returns.show', $purchaseReturn->id)->with('success', _lang('Purchase Returned Sucessfully'));
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $purchase_return = PurchaseReturn::find($id);
		$purchase_return_taxes = PurchaseReturnItemTax::where('purchase_return_id',$id)
													  ->selectRaw('purchase_return_item_taxes.*,sum(purchase_return_item_taxes.amount) as tax_amount')
													  ->groupBy('purchase_return_item_taxes.tax_id')
													  ->get();
		
		return view('backend.accounting.purchase_return.view',compact('purchase_return','purchase_return_taxes','id'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $purchase_return = PurchaseReturn::find($id);
		return view('backend.accounting.purchase_return.edit',compact('purchase_return','id')); 
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
			'return_date' => 'required',
			'cabang_id'     => 'required',
			'supplier_id'   => 'required',
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
				return redirect()->route('purchase_returns.edit', $id)
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
		

        $purchaseReturn                     = PurchaseReturn::find($id);
	    $purchaseReturn->return_date        = $request->input('return_date');
		$purchaseReturn->supplier_id        = $request->input('supplier_id');
		$purchaseReturn->account_id         = "";
		$purchaseReturn->chart_id           = "";
		$purchaseReturn->payment_method_id  = "";
		$purchaseReturn->tax_amount         = $request->tax_total;
		$purchaseReturn->product_total      = $request->input('product_total');
		$purchaseReturn->grand_total        = ($purchaseReturn->product_total + $purchaseReturn->tax_amount);
		$purchaseReturn->attachemnt         = $attachemnt;
		$purchaseReturn->note               = $request->input('note');
        $purchaseReturn->user_id_update     = user_id();
		$purchaseReturn->save();
		
		$taxes = Tax::all();

		//Remove Previous Purcahse item
		$previous_items = PurchaseReturnItem::where("purchase_return_id",$id)->get();
		foreach($previous_items as $p_item){
			$returnItem = PurchaseReturnItem::find($p_item->id);
			
			$hpp = Hpp::where("item_id",$p_item->product_id)
            ->where("gudang_id",$p_item->gudang_id)
            ->where("transaksi_item_id",$p_item->id)
            ->orderby("created_at","desc")->first();
            $hpp->delete();
            
            $hpp_old = Hpp::where("item_id",$p_item->product_id)->where("gudang_id",$p_item->gudang_id)
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
           // return $hpp_old;
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai - $p_item->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
			
			$returnItem->delete();
			update_stock($p_item->product_id);
		}
		
		$purchaseReturnItemTax = PurchaseReturnItemTax::where("purchase_return_id",$id);
		$purchaseReturnItemTax->delete();


		for($i = 0; $i < count($request->product_id); $i++ ){
			$returnItem                     = new PurchaseReturnItem();
			$returnItem->purchase_return_id = $purchaseReturn->id;
			$returnItem->product_id         = $request->product_id[$i];
			$returnItem->gudang_id          = $request->gudang_id[$i];
			$returnItem->description        = "";
			$returnItem->quantity           = $request->quantity[$i];
			$returnItem->unit_cost          = $request->unit_cost[$i];
			$returnItem->discount           = $request->discount[$i];
			$returnItem->tax_amount         = $request->product_tax[$i];
			$returnItem->sub_total          = $request->sub_total[$i];
			
			$hpp_old = Hpp::where("stok_sisa",">=",$request->quantity[$i])
            ->where("item_id",$request->product_id[$i])->where("gudang_id",$request->gudang_id[$i])
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai + $returnItem->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
			
			
			$returnItem->save();
		
		
		    $hpp                        = new Hpp();
            $hpp->transaksi_id          = $purchaseReturn->id;
            $hpp->transaksi_item_id     = $returnItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 7;
            $hpp->stok                  = $hpp->stok + $returnItem->quantity;
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();
			
			//Store Purchase Return Taxes
			if(isset($request->tax[$returnItem->product_id])){
				foreach($request->tax[$returnItem->product_id] as $taxId){
					$tax = $taxes->firstWhere('id', $taxId);
					
					$purchaseReturnItemTax = new PurchaseReturnItemTax();
					$purchaseReturnItemTax->purchase_return_id = $returnItem->purchase_return_id;
					$purchaseReturnItemTax->purchase_return_item_id = $returnItem->id;
					$purchaseReturnItemTax->tax_id = $tax->id;
					$tax_type = $tax->type == 'percent' ? '%' : '';
					$purchaseReturnItemTax->name = $tax->tax_name.' @ '.$tax->rate.$tax_type;
					$purchaseReturnItemTax->amount = $tax->type == 'percent' ? ($returnItem->sub_total / 100) * $tax->rate : $tax->rate;
					$purchaseReturnItemTax->save();
				}
			}

			update_stock($request->product_id[$i]);

		}

		//Update Credit Account
		/*
		$transaction = Transaction::where('purchase_return_id',$purchaseReturn->id)->first();
		$transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = $purchaseReturn->grand_total;
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->purchase_return_id = $purchaseReturn->id;
		$transaction->note = $request->input('note');
		
		$transaction->save();
		*/
		DB::commit();
		
			
        return redirect()->route('purchase_returns.show', $purchaseReturn->id)->with('success', _lang('Updated Sucessfully'));
          
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
		
        $purchaseReturn = PurchaseReturn::find($id);

		//$transaction = Transaction::where('purchase_return_id', $purchaseReturn->id)->first();	
		//$transaction->delete();				

		$purchaseReturn->delete();
		
		//Remove Purchase Item
		$purchaseReturnItems = PurchaseReturnItem::where("purchase_return_id",$id)->get();
		foreach($purchaseReturnItems as $p_item){
			$returnItem = PurchaseReturnItem::find($p_item->id);
			
			$hpp = Hpp::where("item_id",$p_item->product_id)
            ->where("gudang_id",$p_item->gudang_id)
            ->where("transaksi_item_id",$p_item->id)
            ->orderby("created_at","desc")->first();
            $hpp->delete();
            
            $hpp_old = Hpp::where("item_id",$p_item->product_id)->where("gudang_id",$p_item->gudang_id)
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai - $p_item->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
			
			$returnItem->delete();
			update_stock($p_item->product_id);
		}
		
		$purchaseReturnItemTax = PurchaseReturnItemTax::where('purchase_return_id',$id);
		$purchaseReturnItemTax->delete();
		
		DB::commit();

        return back()->with('success',_lang('Deleted Sucessfully'));
	}
		

	public function store_payment(Request $request, $id = '') {
        if ($request->isMethod('get')) {
            $purchase_return = PurchaseReturn::find($id);

            if ($request->ajax()) {
                return view('backend.accounting.purchase_return.modal.create_payment', compact('purchase_return', 'id'));
            }
        }
        if(jenis_langganan()=="POS" || jenis_langganan()=="TRADING"){
            $validator = Validator::make($request->all(), [
                'purchase_return_id'       => 'required',
                'amount'            => 'required|numeric',
                'payment_method_id' => 'required',
                'reference'         => 'nullable|max:50',
                'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            ]);    
        }
        else{
            $validator = Validator::make($request->all(), [
                'purchase_return_id'       => 'required',
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
        if(jenis_langganan()=="POS"||jenis_langganan()=="TRADING"){
            $transaction                    = new Transaction();
            $transaction->trans_date        = date('Y-m-d');
            $transaction->type              = 'income';
            $transaction->dr_cr             = 'cr';
            $transaction->amount            = $request->input('amount');
            $transaction->payment_method_id = $request->input('payment_method_id');
            $transaction->purchase_return_id       = $request->input('purchase_return_id');
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
            $transaction->type              = 'income';
            $transaction->dr_cr             = 'cr';
            $transaction->amount            = $request->input('amount');
            $transaction->payment_method_id = $request->input('payment_method_id');
            $transaction->purchase_return_id       = $request->input('purchase_return_id');
            $transaction->reference         = $request->input('reference');
            $transaction->note              = $request->input('note');
            $transaction->attachment        = $attachment;
            $transaction->user_id           = user_id();
        }
        $transaction->user_id        = user_id();
        $transaction->save();

        //Update Pembelian Order Table
        $purchase_return       = PurchaseReturn::find($transaction->purchase_return_id);
        $purchase_return->paid = $purchase_return->paid + $transaction->amount;
        if (round($purchase_return->paid, 2) >= $purchase_return->grand_total) {
            $purchase_return->payment_status = 1;
        }
        $purchase_return->save();

        if ($request->ajax()) {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment was made Sucessfully'), 'data' => $transaction]);
        }
    }

	public function view_payment(Request $request, $purchase_return_id) {

        $transactions = Transaction::where("purchase_return_id", $purchase_return_id)->get();

        if (!$request->ajax()) {
            return view('backend.accounting.purchase_return.view_payment', compact('transactions'));
        } else {
            return view('backend.accounting.purchase_return.modal.view_payment', compact('transactions'));
        }
    }

	
}
