<?php

namespace App\Http\Controllers;

use App\Purchase;
use App\PurchaseOrderItem;
use App\PurchaseOrderItemTax;
use App\Pembelian;
use App\PembelianItem;
use App\PembelianItemTax;
use App\Stock;
use App\Tax;
use App\Hpp;
use App\Transaction;
use DataTables;
use DB;
use Illuminate\Http\Request;
use PDF;
use Validator;

class PurchaseController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.accounting.purchase_order.list');
    }

    public function get_table_data(Request $request) {
        $currency = currency();

        $purchases = Purchase::with('supplier')
            ->orderBy("id", "desc");

        return Datatables::eloquent($purchases)
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

            ->editColumn('order_status', function ($purchase) {
                if ($purchase->order_status == 1) {
                    return '<span class="badge badge-info">' . _lang('Ordered') . '</span>';
                } else if ($purchase->order_status == 2) {
                    return '<span class="badge badge-danger">' . _lang('Pending') . '</span>';
                } else if ($purchase->order_status == 3) {
                    return '<span class="badge badge-success">' . _lang('Received') . '</span>';
                }
            })
            ->editColumn('grand_total', function ($purchase) use ($currency) {
                return '<span class="float-right">' . decimalPlace($purchase->grand_total, $currency) . '</span>';
            })
            

            ->addColumn('action', function ($purchase) {
                $return =  '<div class="dropdown text-center">'
                . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '&nbsp;<i class="fas fa-angle-down"></i></button>'
                . '<div class="dropdown-menu">';
                if ($purchase->order_status != 3) {
                    $return = $return . '<a class="dropdown-item" href="' . action('PurchaseController@edit', $purchase->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>';
                    $return = $return . '<a class="dropdown-item" href="' . action('PurchaseController@convert_pembelian', $purchase->id) . '"><i class="ti-exchange-vertical"></i> ' . _lang('Konversikan Ke Pembelian') . '</a></li>';
                
                }
                
                $return = $return . '<a class="dropdown-item" href="' . action('PurchaseController@show', $purchase->id) . '" data-title="' . _lang('View Invoice') . '" data-fullscreen="true"><i class="ti-eye"></i> ' . _lang('View') . '</a>';
                
                if ($purchase->order_status != 3) {
                    $return = $return. '<form action="' . action('PurchaseController@destroy', $purchase['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>';
                }
                $return = $return . '</div>'
                    . '</div>';
                return $return;
            })
            ->setRowId(function ($purchase) {
                return "row_" . $purchase->id;
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
            return view('backend.accounting.purchase_order.create');
        } else {
            return view('backend.accounting.purchase_order.modal.create');
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
            'order_status'   => 'required',
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
                return redirect()->route('purchase_orders.create')
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

        $purchase                 = new Purchase();
        $purchase->invoice_number = $request->input('invoice_number');
        $purchase->order_date     = $request->input('order_date');
        $purchase->supplier_id    = $request->input('supplier_id');
        $purchase->cabang_id      = $request->input('cabang_id');
        $purchase->order_status   = $request->input('order_status');
        $purchase->order_discount = $request->input('order_discount');
        $purchase->shipping_cost  = $request->input('shipping_cost');
        $purchase->product_total  = $request->input('product_total');
        $purchase->order_tax      = $request->tax_total;
        $purchase->grand_total    = ($purchase->product_total + $purchase->shipping_cost + $purchase->order_tax) - $purchase->order_discount;
        $purchase->paid           = 0;
        $purchase->payment_status = 0;
        $purchase->attachemnt     = $attachemnt;
        $purchase->note           = $request->input('note');
        $purchase->user_id        = user_id();

        $purchase->save();

        $taxes = Tax::all();

        //Save Purcahse item
        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem                    = new PurchaseOrderItem();
            $purchaseItem->purchase_order_id = $purchase->id;
            $purchaseItem->product_id        = $request->product_id[$i];
            $purchaseItem->gudang_id         = $request->gudang_id[$i];
            $purchaseItem->description       = "";
            $purchaseItem->quantity          = $request->quantity[$i];
            $purchaseItem->unit_cost         = $request->unit_cost[$i];
            $purchaseItem->discount          = $request->discount[$i];
            $purchaseItem->tax_amount        = $request->product_tax[$i];
            $purchaseItem->sub_total         = $request->sub_total[$i];
            $purchaseItem->save();

            //Store Purchase Order Taxes
            if (isset($request->tax[$purchaseItem->product_id])) {
                foreach ($request->tax[$purchaseItem->product_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $purchaseOrderItemTax                         = new PurchaseOrderItemTax();
                    $purchaseOrderItemTax->purchase_order_id      = $purchaseItem->purchase_order_id;
                    $purchaseOrderItemTax->purchase_order_item_id = $purchaseItem->id;
                    $purchaseOrderItemTax->tax_id                 = $tax->id;
                    $tax_type                                     = $tax->type == 'percent' ? '%' : '';
                    $purchaseOrderItemTax->name                   = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $purchaseOrderItemTax->amount                 = $tax->type == 'percent' ? ($purchaseItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $purchaseOrderItemTax->save();
                }
            }

            //Update Stock if Order Status is received
            if ($request->input('order_status') == '3') {
                $stock           = Stock::where("product_id", $purchaseItem->product_id)->first();
                $stock->quantity = $stock->quantity + $purchaseItem->quantity;
                $stock->save();
            }
        }
        if (get_company_option('invoice_order_pembelian')!=""){
            //Increment Invoice Starting number
            increment_orderpembelian_number();
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('purchase_orders.show', $purchase->id)->with('success', _lang('Purchase Order Created Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Purchase Order Created Sucessfully'), 'data' => $purchase]);
        }

    }


    public function convert_pembelian($id){
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);
    
            DB::beginTransaction();
    
            $purchase_orders = Purchase::where('id', $id)->where('status', 0)->first();
    
            if (!$purchase_orders) {
                return back()->with('error', _lang('Sorry, Purchase_orders is already converted to Pembelian !'));
            }
    
            $pembelian                 = new Pembelian();
            $pembelian->order_date     = date('Y-m-d');
            $pembelian->supplier_id    = $purchase_orders->supplier_id;
            $pembelian->invoice_number = get_company_option('invoice_pembelian_perfix') . get_company_option('invoice_pembelian');
            $pembelian->cabang_id      = $purchase_orders->cabang_id;
            $pembelian->order_status   = "0";
            $pembelian->order_discount = $purchase_orders->order_discount;
            $pembelian->shipping_cost  = $purchase_orders->shipping_cost;
            $pembelian->product_total  = $purchase_orders->product_total;
            $pembelian->order_tax      = $purchase_orders->tax_total;
            $pembelian->grand_total    = $purchase_orders->grand_total;
            $pembelian->paid           = 0;
            $pembelian->payment_status = 0;
            $pembelian->user_id        = user_id();
            $pembelian->po_number      = $purchase_orders->id;
            $pembelian->attachemnt     = $purchase_orders->attachemnt;
            $pembelian->note           = $purchase_orders->note;
            $pembelian->user_id        = user_id();
    
            $pembelian->save();
            $taxes = Tax::all();

            //Save Purcahse item
            foreach ($purchase_orders->purchase_items as $purchase_orders_item) {
                $pembelianItem                    = new PembelianItem();
                $pembelianItem->pembelian_id      = $pembelian->id;
                $pembelianItem->product_id        = $purchase_orders_item->product_id;
                $pembelianItem->gudang_id         = $purchase_orders_item->gudang_id;
                $pembelianItem->description       = $purchase_orders_item->description;
                $pembelianItem->quantity          = $purchase_orders_item->quantity;
                $pembelianItem->unit_cost         = $purchase_orders_item->unit_cost;
                $pembelianItem->discount          = $purchase_orders_item->discount;
                $pembelianItem->tax_amount        = $purchase_orders_item->product_tax;
                $pembelianItem->sub_total         = $purchase_orders_item->sub_total;
                $pembelianItem->save();
    
    
    
                $hpp                        = new Hpp();
                $hpp->transaksi_id          = $pembelian->id;
                $hpp->transaksi_item_id     = $pembelianItem->id;
                $hpp->item_id               = $purchase_orders_item->product_id;
                $hpp->invoice_number        = $pembelian->invoice_number;
                $hpp->gudang_id             = $purchase_orders_item->gudang_id;
                $hpp->cabang_id             = $purchase_orders->cabang_id;
                $hpp->flag                  = 1;
                $hpp->stok                  = $hpp->stok + $pembelianItem->quantity;
                $hpp->stok_sisa             = $hpp->stok-$hpp->stok_terpakai; 
                $hpp->harga                 = $purchase_orders_item->sub_total;
                $hpp->save();
    
                //Store Pembelian Taxes
                foreach ($purchase_orders_item->taxes as $purchase_orders_tax) {
                        $tax = $taxes->firstWhere('id', $purchase_orders_tax->tax_id);

                        $PembelianItemTax                         = new PembelianItemTax();
                        $PembelianItemTax->purchase_order_id      = $PembelianItem->pembelian_id;
                        $PembelianItemTax->pembelian_item_id      = $PembelianItem->id;
                        $PembelianItemTax->tax_id                 = $tax->id;
                        $tax_type                                 = $tax->type == 'percent' ? '%' : '';
                        $PembelianItemTax->name                   = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                        $PembelianItemTax->amount                 = $tax->type == 'percent' ? ($purchaseItem->sub_total / 100) * $tax->rate : $tax->rate;
                        $PembelianItemTax->save();
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

            $purchase_orders->order_status     = 3;
            $purchase_orders->save();

            DB::commit();
    
            return redirect('purchase_orders/' . $purchase_orders->id)->with('success', _lang('Purchase Order Converted Sucessfully'));
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $purchase       = Purchase::where("id", $id)->where("company_id", company_id())->first();
        $purchase_taxes = PurchaseOrderItemTax::where('purchase_order_id', $id)
            ->selectRaw('purchase_order_item_taxes.*,sum(purchase_order_item_taxes.amount) as tax_amount')
            ->groupBy('purchase_order_item_taxes.tax_id')
            ->get();
        $transactions = Transaction::where("purchase_id", $id)
            ->where("company_id", company_id())->get();

        return view('backend.accounting.purchase_order.view', compact('purchase', 'purchase_taxes', 'transactions', 'id'));

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
        $data['purchase']       = Purchase::where("id", $id)->where("company_id", company_id())->first();
        $data['purchase_taxes'] = PurchaseOrderItemTax::where('purchase_order_id', $id)
            ->selectRaw('purchase_order_item_taxes.*,sum(purchase_order_item_taxes.amount) as tax_amount')
            ->groupBy('purchase_order_item_taxes.tax_id')
            ->get();
        $data['transactions'] = Transaction::where("purchase_id", $id)->get();

        $pdf = PDF::loadView("backend.accounting.purchase_order.pdf_export", $data);
        $pdf->setWarnings(false);

        return $pdf->stream();
        return $pdf->download("purchase_order_{$data['purchase']->id}.pdf");

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $purchase = Purchase::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.purchase_order.edit', compact('purchase', 'id'));
        } else {
            return view('backend.accounting.purchase_order.modal.edit', compact('purchase', 'id'));
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
            'cabang_id'      => 'required',
            'order_status'   => 'required',
            'order_discount' => 'nullable|numeric',
            'shipping_cost'  => 'nullable|numeric',
            'sub_total.*'    => 'required|numeric',
            'attachemnt'     => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('purchase_orders.edit', $id)
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

        $purchase = Purchase::find($id);

        $previous_status = $purchase->order_status;

        $purchase->order_date     = $request->input('order_date');
        $purchase->supplier_id    = $request->input('supplier_id');
        $purchase->cabang_id      = $request->input('cabang_id');
        $purchase->order_status   = $request->input('order_status');
        $purchase->order_discount = $request->input('order_discount');
        $purchase->shipping_cost  = $request->input('shipping_cost');
        $purchase->product_total  = $request->input('product_total');
        $purchase->order_tax      = $request->tax_total;
        $purchase->grand_total    = ($purchase->product_total + $purchase->shipping_cost + $purchase->order_tax) - $purchase->order_discount;
        $purchase->user_id_update  = user_id();
        $purchase->payment_status = $request->input('payment_status');

        if (round($purchase->paid, 2) < $purchase->grand_total) {
            $purchase->payment_status = 0;
        }

        if ($request->hasfile('attachemnt')) {
            $purchase->attachemnt = $attachemnt;
        }
        $purchase->note = $request->input('note');

        $purchase->save();

        $taxes = Tax::all();

        //Update Purcahse item
        $purchaseItems = PurchaseOrderItem::where("purchase_order_id", $id)->get();
        foreach ($purchaseItems as $p_item) {
            $orderItem = PurchaseOrderItem::find($p_item->id);
            $orderItem->delete();
            update_stock($p_item->product_id);
        }

        $purchaseOrderItemTax = PurchaseOrderItemTax::where("purchase_order_id", $id);
        $purchaseOrderItemTax->delete();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem                    = new PurchaseOrderItem();
            $purchaseItem->purchase_order_id = $purchase->id;
            $purchaseItem->product_id        = $request->product_id[$i];
            $purchaseItem->gudang_id         = $request->gudang_id[$i];
            $purchaseItem->description       = "";
            $purchaseItem->quantity          = $request->quantity[$i];
            $purchaseItem->unit_cost         = $request->unit_cost[$i];
            $purchaseItem->discount          = $request->discount[$i];
            $purchaseItem->tax_amount        = $request->product_tax[$i];
            $purchaseItem->sub_total         = $request->sub_total[$i];
            $purchaseItem->save();

            //Store Purchase Order Taxes
            if (isset($request->tax[$purchaseItem->product_id])) {
                foreach ($request->tax[$purchaseItem->product_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $purchaseOrderItemTax                         = new PurchaseOrderItemTax();
                    $purchaseOrderItemTax->purchase_order_id      = $purchaseItem->purchase_order_id;
                    $purchaseOrderItemTax->purchase_order_item_id = $purchaseItem->id;
                    $purchaseOrderItemTax->tax_id                 = $tax->id;
                    $tax_type                                     = $tax->type == 'percent' ? '%' : '';
                    $purchaseOrderItemTax->name                   = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $purchaseOrderItemTax->amount                 = $tax->type == 'percent' ? ($purchaseItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $purchaseOrderItemTax->save();
                }
            }

            //Update Stock if Order Status is received
            if ($request->input('order_status') == '3') {
                update_stock($request->product_id[$i]);
            }

        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('purchase_orders.show', $purchase->id)->with('success', _lang('Purchase Order Updated Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Purchase Order Updated Sucessfully'), 'data' => $purchase]);
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

        $purchase = Purchase::find($id);
        $purchase->delete();

        //Remove Purchase Item
        $purchaseItems = PurchaseOrderItem::where("purchase_order_id", $id)->get();
        foreach ($purchaseItems as $p_item) {
            $returnItem = PurchaseOrderItem::find($p_item->id);
            $returnItem->delete();
            update_stock($p_item->product_id);
        }

        $purchaseOrderItemTax = PurchaseOrderItemTax::where('purchase_order_id', $id);
        $purchaseOrderItemTax->delete();

        DB::commit();

        return back()->with('success', _lang('Deleted Sucessfully'));
    }

    public function store_payment(Request $request, $id = '') {
        if ($request->isMethod('get')) {
            $purchase = Purchase::find($id);

            if ($request->ajax()) {
                return view('backend.accounting.purchase_order.modal.create_payment', compact('purchase', 'id'));
            }
        }

        $validator = Validator::make($request->all(), [
            'purchase_id'       => 'required',
            'account_id'        => 'required',
            'chart_id'          => 'required',
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required',
            'reference'         => 'nullable|max:50',
            'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
        ]);

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

        $transaction                    = new Transaction();
        $transaction->trans_date        = date('Y-m-d');
        $transaction->account_id        = $request->input('account_id');
        $transaction->chart_id          = $request->input('chart_id');
        $transaction->type              = 'expense';
        $transaction->dr_cr             = 'dr';
        $transaction->amount            = $request->input('amount');
        $transaction->payment_method_id = $request->input('payment_method_id');
        $transaction->purchase_id       = $request->input('purchase_id');
        $transaction->reference         = $request->input('reference');
        $transaction->note              = $request->input('note');
        $transaction->attachment        = $attachment;

        $transaction->save();

        //Update Purchase Order Table
        $purchase       = Purchase::find($transaction->purchase_id);
        $purchase->paid = $purchase->paid + $transaction->amount;
        if (round($purchase->paid, 2) >= $purchase->grand_total) {
            $purchase->payment_status = 1;
        }
        $purchase->save();

        if ($request->ajax()) {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment was made Sucessfully'), 'data' => $transaction]);
        }
    }

    public function view_payment(Request $request, $purchase_id) {

        $transactions = Transaction::where("purchase_id", $purchase_id)->get();

        if (!$request->ajax()) {
            return view('backend.accounting.purchase_order.view_payment', compact('transactions'));
        } else {
            return view('backend.accounting.purchase_order.modal.view_payment', compact('transactions'));
        }
    }

}
