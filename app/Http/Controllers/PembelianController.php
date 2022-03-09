<?php

namespace App\Http\Controllers;

use App\Pembelian;
use App\PembelianItem;
use App\PembelianItemTax;
use App\Stock;
use App\Hpp;
use App\Tax;
use App\Transaction;
use App\Purchase;
use DataTables;
use DB;
use Illuminate\Http\Request;
use PDF;
use Validator;

class PembelianController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.accounting.pembelian.list');
    }

    public function get_table_data(Request $request) {
        $currency = currency();

        $pembelian = Pembelian::with('supplier')
            ->orderBy("id", "desc");

        return Datatables::eloquent($pembelian)
            ->filter(function ($query) use ($request) {
                if ($request->has('supplier_id')) {
                    $query->where('supplier_id', $request->get('supplier_id'));
                }
                
                if ($request->has('cabang_id')) {
                    $query->where('cabang_id', $request->get('cabang_id'));
                }

                if ($request->has('order_status')) {
                    $query->whereIn('order_status', json_decode($request->get('order_status')));
                }
                
                if ($request->has('payment_status')) {
                    $query->whereIn('payment_status', json_decode($request->get('payment_status')));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->get('date_range'));
                    $query->whereBetween('order_date', [$date_range[0], $date_range[1]]);
                }
            })

            ->editColumn('order_status', function ($pembelian) {
                if ($pembelian->order_status == 1) {
                    return '<span class="badge badge-info">' . _lang('Ordered') . '</span>';
                } else if ($pembelian->order_status == 2) {
                    return '<span class="badge badge-danger">' . _lang('Pending') . '</span>';
                } else if ($pembelian->order_status == 3) {
                    return '<span class="badge badge-success">' . _lang('Received') . '</span>';
                }
            })
            ->editColumn('grand_total', function ($pembelian) use ($currency) {
                return '<span class="float-right">' . decimalPlace($pembelian->grand_total, $currency) . '</span>';
            })
            
            ->editColumn('paid', function ($pembelian) use ($currency) {
                return '<span class="float-right">' . decimalPlace($pembelian->paid, $currency) . '</span>';
            })
            ->editColumn('payment_status', function ($purchase) {
                if ($purchase->payment_status == 0) {
                    return '<span class="badge badge-danger">' . _lang('Blum Lunas') . '</span>';
                } else {
                    return '<span class="badge badge-success">' . _lang('Lunas') . '</span>';
                }
            })
            ->addColumn('action', function ($pembelian) {
                if ($pembelian->payment_status==1){
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;<i class="fas fa-angle-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('PembelianController@show', $pembelian->id) . '" data-title="' . _lang('View Invoice') . '" data-fullscreen="true"><i class="ti-eye"></i> ' . _lang('View') . '</a>'
                    . '<a href="' . route('pembelian.view_payment', $pembelian->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
                    . '</div>'
                    . '</div>';
                }
                else{
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;<i class="fas fa-angle-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('PembelianController@edit', $pembelian->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . action('PembelianController@show', $pembelian->id) . '" data-title="' . _lang('View Invoice') . '" data-fullscreen="true"><i class="ti-eye"></i> ' . _lang('View') . '</a>'
                    . '<a href="' . route('pembelian.create_payment', $pembelian->id) . '" data-title="' . _lang('Make Payment') . '" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('Make Payment') . '</a>'
                    . '<a href="' . route('pembelian.view_payment', $pembelian->id) . '" data-title="' . _lang('View Payments') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment History') . '</a>'
                    . '<form action="' . action('PembelianController@destroy', $pembelian['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
                }
            })
            ->setRowId(function ($pembelian) {
                return "row_" . $pembelian->id;
            })
            ->rawColumns(['action', 'grand_total', 'paid', 'order_status', 'payment_status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.pembelian.create');
        } else {
            return view('backend.accounting.pembelian.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'order_date'     => 'required',
            'supplier_id'    => 'required',
            'order_discount' => 'nullable|numeric',
            'shipping_cost'  => 'nullable|numeric',
            'sub_total.*'    => 'required|numeric',
            'attachemnt'     => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            'product_id'     => 'required',
        ], [
            'product_id.required' => _lang('You must select at least one product or service'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('pembelian.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        $attachemnt = "";
        if ($request->hasfile('attachemnt')) {
            $file       = $request->file('attachemnt');
            $attachemnt = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/attachments/", $attachemnt);
        }

        $pembelian                 = new Pembelian();
        $pembelian->order_date     = $request->input('order_date');
        $pembelian->supplier_id    = $request->input('supplier_id');
        $pembelian->invoice_number = $request->input('invoice_number');
        $pembelian->cabang_id      = $request->input('cabang_id');
        $pembelian->order_status   = "3";
        $pembelian->order_discount = $request->input('order_discount');
        $pembelian->shipping_cost  = $request->input('shipping_cost');
        $pembelian->product_total  = $request->input('product_total');
        $pembelian->order_tax      = $request->tax_total;
        $pembelian->grand_total    = ($pembelian->product_total + $pembelian->shipping_cost + $pembelian->order_tax) - $pembelian->order_discount;
        $pembelian->paid           = 0;
        $pembelian->payment_status = 0;
        $pembelian->user_id        = user_id();
        $pembelian->attachemnt     = $attachemnt;
        $pembelian->note           = $request->input('note');

        $pembelian->save();

        $taxes = Tax::all();

        //Save Purcahse item
        for ($i = 0; $i < count($request->product_id); $i++) {
            $pembelianItem                    = new PembelianItem();
            $pembelianItem->pembelian_id      = $pembelian->id;
            $pembelianItem->product_id        = $request->product_id[$i];
            $pembelianItem->gudang_id         = $request->gudang_id[$i];
            $pembelianItem->description       = "";
            $pembelianItem->quantity          = $request->quantity[$i];
            $pembelianItem->unit_cost         = $request->unit_cost[$i];
            $pembelianItem->discount          = $request->discount[$i];
            $pembelianItem->tax_amount        = $request->product_tax[$i];
            $pembelianItem->sub_total         = $request->sub_total[$i];
            $pembelianItem->save();



            $hpp                        = new Hpp();
            $hpp->transaksi_id          = $pembelian->id;
            $hpp->transaksi_item_id     = $pembelianItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 1;
            $hpp->stok                  = $hpp->stok + $pembelianItem->quantity;
            $hpp->stok_sisa             = $hpp->stok-$hpp->stok_terpakai; 
            $hpp->harga                 = $request->sub_total[$i]/ $request->quantity[$i];
            $hpp->save();

            //Store Pembelian Taxes
            if (isset($request->tax[$pembelianItem->product_id])) {
                foreach ($request->tax[$pembelianItem->product_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $PembelianItemTax                         = new PembelianItemTax();
                    $PembelianItemTax->purchase_order_id      = $PembelianItem->pembelian_id;
                    $PembelianItemTax->pembelian_item_id      = $PembelianItem->id;
                    $PembelianItemTax->tax_id                 = $tax->id;
                    $tax_type                                 = $tax->type == 'percent' ? '%' : '';
                    $PembelianItemTax->name                   = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $PembelianItemTax->amount                 = $tax->type == 'percent' ? ($purchaseItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $PembelianItemTax->save();
                }
            }

            //Update Stock if Order Status is received
            //if ($request->input('order_status') == '3') {
                $stock           = Stock::where("product_id", $pembelianItem->product_id)->first();
                $stock->quantity = $stock->quantity + $pembelianItem->quantity;
                $stock->save();   

            //}
        
            
        }
        
        if(is_numeric(get_company_option('invoice_pembelian'))==true){
            increment_pembelian_number();
        }
        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('pembelian.show', $pembelian->id)->with('success', _lang('Purchase Created Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Purchase Created Sucessfully'), 'data' => $pembelian]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $pembelian       = Pembelian::where("id", $id)->where("company_id", company_id())->first();
        $pembelian_taxes = PembelianItemTax::where('pembelian_id', $id)
            ->selectRaw('pembelian_item_taxes.*,sum(pembelian_item_taxes.amount) as tax_amount')
            ->groupBy('pembelian_item_taxes.tax_id')
            ->get();
        $transactions = Transaction::where("purchase_id", $id)
            ->where("company_id", company_id())->get();

        return view('backend.accounting.pembelian.view', compact('pembelian', 'pembelian_taxes', 'transactions', 'id'));

    }

    /**
     * Generate PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download_pdf(Request $request, $id) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $data                   = array();
        $data['pembelian']       = Pembelian::where("id", $id)->where("company_id", company_id())->first();
        $data['pembelian_taxes'] = PembelianItemTax::where('pembelian_id', $id)
            ->selectRaw('pembelian_item_taxes.*,sum(pembelian_item_taxes.amount) as tax_amount')
            ->groupBy('pembelian_item_taxes.tax_id')
            ->get();
        $data['transactions'] = Transaction::where("id", $id)->get();

        $pdf = PDF::loadView("backend.accounting.pembelian.pdf_export", $data);
        $pdf->setWarnings(false);

        return $pdf->stream();
        return $pdf->download("pembelian_{$data['pembelian']->id}.pdf");

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $pembelian = Pembelian::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.pembelian.edit', compact('pembelian', 'id'));
        } else {
            return view('backend.accounting.pembelian.modal.edit', compact('pembelian', 'id'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'order_date'     => 'required',
            'supplier_id'    => 'required',
            'order_discount' => 'nullable|numeric',
            'shipping_cost'  => 'nullable|numeric',
            'sub_total.*'    => 'required|numeric',
            'attachemnt'     => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('pembelian.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        if ($request->hasfile('attachemnt')) {
            $file       = $request->file('attachemnt');
            $attachemnt = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/attachments/", $attachemnt);
        }

        $pembelian = Pembelian::find($id);

        $previous_status = $pembelian->order_status;

        $pembelian->order_date     = $request->input('order_date');
        $pembelian->supplier_id    = $request->input('supplier_id');
        $pembelian->order_status   = $request->input('order_status');
        $pembelian->order_discount = $request->input('order_discount');
        $pembelian->shipping_cost  = $request->input('shipping_cost');
        $pembelian->product_total  = $request->input('product_total');
        $pembelian->order_tax      = $request->tax_total;
        $pembelian->user_id_update = user_id();
        $pembelian->grand_total    = ($pembelian->product_total + $pembelian->shipping_cost + $pembelian->order_tax) - $pembelian->order_discount;

        $pembelian->payment_status = $request->input('payment_status');
        if (round($pembelian->paid, 2) < $pembelian->grand_total) {
            $pembelian->payment_status = 0;
        }

        if ($request->hasfile('attachemnt')) {
            $pembelian->attachemnt = $attachemnt;
        }
        $pembelian->note = $request->input('note');

        $pembelian->save();

        $taxes = Tax::all();

        //Update Pembelian item
        $pembelianItems = PembelianItem::where("pembelian_id", $id)->get();
        foreach ($pembelianItems as $p_item) {
            $hpp = Hpp::where("transaksi_item_id", $p_item->id)->where("invoice_number",$pembelian->invoice_number)->first();
            if ($hpp){
                $hpp->delete();
            }
            $pembelianItem = PembelianItem::find($p_item->id);
            $pembelianItem->delete();
            update_stock($p_item->product_id);
        }

        $PembelianItemTax = PembelianItemTax::where("pembelian_id", $id);
        $PembelianItemTax->delete();
        
        
        if ($request->product_id!=0){
            for ($i = 0; $i < count($request->product_id); $i++) {
                $pembelianItem                    = new PembelianItem();
                $pembelianItem->pembelian_id      = $pembelian->id;
                $pembelianItem->product_id        = $request->product_id[$i];
                $pembelianItem->gudang_id         = $request->gudang_id[$i];
                $pembelianItem->description       = "";
                $pembelianItem->quantity          = $request->quantity[$i];
                $pembelianItem->unit_cost         = $request->unit_cost[$i];
                $pembelianItem->discount          = $request->discount[$i];
                $pembelianItem->tax_amount        = $request->product_tax[$i];
                $pembelianItem->sub_total         = $request->sub_total[$i];
                $pembelianItem->save();
    
                //Store Pembelian Order Taxes
                if (isset($request->tax[$pembelianItem->product_id])) {
                    foreach ($request->tax[$pembelianItem->product_id] as $taxId) {
                        $tax = $taxes->firstWhere('id', $taxId);
    
                        $pembelianItemTax                         = new PembelianItemTax();
                        $pembelianItemTax->pembelian_id           = $pembelianItem->pembelian_id;
                        $PembelianItemTax->pembelian_item_id      = $pembelianItem->id;
                        $PembelianItemTax->tax_id                 = $tax->id;
                        $tax_type                                 = $tax->type == 'percent' ? '%' : '';
                        $PembelianItemTax->name                   = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                        $PembelianItemTax->amount                 = $tax->type == 'percent' ? ($pembelianItem->sub_total / 100) * $tax->rate : $tax->rate;
                        $PembelianItemTax->save();
                    }
                }
    
                $hpp                        = new Hpp();
                $hpp->transaksi_id          = $pembelian->id;
                $hpp->transaksi_item_id     = $pembelianItem->id;
                $hpp->item_id               = $request->product_id[$i];
                $hpp->invoice_number        = $pembelian->invoice_number;
                $hpp->gudang_id             = $request->gudang_id[$i];
                $hpp->cabang_id             = $request->input('cabang_id');
                $hpp->flag                  = 1;
                $hpp->stok                  = $hpp->stok + $pembelianItem->quantity;
                $hpp->stok_sisa             = $hpp->stok-$hpp->stok_terpakai; 
                $hpp->harga                 = $request->sub_total[$i]/$pembelianItem->quantity;
                $hpp->save();
                
    
                //Update Stock if Order Status is received
                //if ($request->input('order_status') == '3') {
                    update_stock($request->product_id[$i]);
                //}
    
            }
        }
        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('pembelian.show', $pembelian->id)->with('success', _lang('Purchase Updated Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Purchase Updated Sucessfully'), 'data' => $pembelian]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::beginTransaction();

        $pembelian = Pembelian::find($id);

        if ($pembelian->po_number!=""){
            $purchase = Purchase::find($pembelian->po_number,'id');
            $purchase->order_status="1";
            $purchase->save();
               
        }

        $pembelian->delete();


        $transaksi = Transaction::where("pembelian_id",$id);
        if ($transaksi){
            $transaksi->delete();
        }
        //Remove Pembelian Item
        $pembelianItems = PembelianItem::where("pembelian_id", $id)->get();
        foreach ($pembelianItems as $p_item) {
            $returnItem = PembelianItem::find($p_item->id);
            $hpp = Hpp::where("transaksi_item_id", $p_item->id)->where("invoice_number",$pembelian->invoice_number)->first();
            if ($hpp){
                $hpp->delete();
            }
            $returnItem->delete();
            update_stock($p_item->product_id);
        }

        $PembelianItemTax = PembelianItemTax::where('pembelian_id', $id);
        $PembelianItemTax->delete();

        DB::commit();

        return back()->with('success', _lang('Deleted Sucessfully'));
    }

    public function store_payment(Request $request, $id = '') {
        if ($request->isMethod('get')) {
            $pembelian = Pembelian::find($id);

            if ($request->ajax()) {
                return view('backend.accounting.pembelian.modal.create_payment', compact('pembelian', 'id'));
            }
        }
        if(jenis_langganan()=="POS" || jenis_langganan()=="TRADING"){
            $validator = Validator::make($request->all(), [
                'pembelian_id'       => 'required',
                'amount'            => 'required|numeric',
                'payment_method_id' => 'required',
                'reference'         => 'nullable|max:50',
                'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            ]);    
        }
        else{
            $validator = Validator::make($request->all(), [
                'pembelian_id'       => 'required',
                'account_id'        => 'required',
                'chart_id'          => 'required',
                'amount'            => 'required|numeric',
                'payment_method_id' => 'required',
                'reference'         => 'nullable|max:50',
                'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
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
            $transaction->type              = 'expense';
            $transaction->dr_cr             = 'dr';
            $transaction->amount            = $request->input('amount');
            $transaction->payment_method_id = $request->input('payment_method_id');
            $transaction->pembelian_id       = $request->input('pembelian_id');
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
            $transaction->pembelian_id       = $request->input('pembelian_id');
            $transaction->reference         = $request->input('reference');
            $transaction->note              = $request->input('note');
            $transaction->attachment        = $attachment;
            $transaction->user_id           = user_id();
        }
        $transaction->user_id        = user_id();
        $transaction->save();

        //Update Pembelian Order Table
        $pembelian       = Pembelian::find($transaction->pembelian_id);
        $pembelian->paid = $pembelian->paid + $transaction->amount;
        if (round($pembelian->paid, 2) >= $pembelian->grand_total) {
            $pembelian->payment_status = 1;
        }
        $pembelian->save();

        if ($request->ajax()) {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment was made Sucessfully'), 'data' => $transaction]);
        }
    }

    public function view_payment(Request $request, $pembelian_id) {

        $transactions = Transaction::where("pembelian_id", $pembelian_id)->get();

        if (!$request->ajax()) {
            return view('backend.accounting.pembelian.view_payment', compact('transactions'));
        } else {
            return view('backend.accounting.pembelian.modal.view_payment', compact('transactions'));
        }
    }

}
