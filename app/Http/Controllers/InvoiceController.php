<?php

namespace App\Http\Controllers;

use App\CompanySetting;
use App\Invoice;
use App\InvoiceItem;
use App\InvoiceItemTax;
use App\Mail\GeneralMail;
use App\Quotation;
use App\Stock;
use App\Tax;
use App\Hpp;
use App\Transaction;
use App\Utilities\Overrider;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PDF;
use Validator;

class InvoiceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.accounting.invoice.list');
    }

    public function get_table_data(Request $request) {

        $currency = currency();

        $invoices = Invoice::with("client")
            ->select('invoices.*')
            ->orderBy("invoices.id", "desc");

        return Datatables::eloquent($invoices)
            ->filter(function ($query) use ($request) {
                if ($request->has('invoice_number')) {
                    $query->where('invoice_number', 'like', "%{$request->get('invoice_number')}%");
                }

                if ($request->has('client_id')) {
                    $query->where('client_id', $request->get('client_id'));
                }

                if ($request->has('cabang_id')) {
                    $query->where('cabang_id', $request->get('cabang_id'));
                }

                if ($request->has('status')) {
                    $query->whereIn('status', json_decode($request->get('status')));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->get('date_range'));
                    $query->whereBetween('invoice_date', [$date_range[0], $date_range[1]]);
                }
            })
            ->editColumn('grand_total', function ($invoice) use ($currency) {
                return "<span class='float-right'>" . decimalPlace($invoice->grand_total, $currency) . "</span>";
            })
            ->editColumn('status', function ($invoice) {
                return invoice_status($invoice->status);
            })
            ->addColumn('action', function ($invoice) {
                if ($invoice->status=="Paid"){
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;<i class="fas fa-angle-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('InvoiceController@show', $invoice->id) . '" data-title="' . _lang('View Invoice') . '" data-fullscreen="true"><i class="ti-eye"></i> ' . _lang('View') . '</a>'
                    . '<a href="' . route('invoices.view_payment', $invoice->id) . '" data-title="' . _lang('View Payment') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment') . '</a>'
                        . '</div>'
                        . '</div>';
    
                }
                else{
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;<i class="fas fa-angle-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('InvoiceController@edit', $invoice->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . action('InvoiceController@show', $invoice->id) . '" data-title="' . _lang('View Invoice') . '" data-fullscreen="true"><i class="ti-eye"></i> ' . _lang('View') . '</a>'
                    . '<a href="' . route('invoices.create_payment', $invoice->id) . '" data-title="' . _lang('Make Payment') . '" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('Make Payment') . '</a>'
                    . '<a href="' . route('invoices.view_payment', $invoice->id) . '" data-title="' . _lang('View Payment') . '" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="ti-credit-card"></i> ' . _lang('View Payment') . '</a>'
                    . '<form action="' . action('InvoiceController@destroy', $invoice['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                        . '</form>'
                        . '</div>'
                        . '</div>';
                }
            })
            ->setRowId(function ($invoice) {
                return "row_" . $invoice->id;
            })
            ->rawColumns(['grand_total', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.invoice.create');
        } else {
            return view('backend.accounting.invoice.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if (jenis_langganan()=="POS"){
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'invoice_number' => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]);
        }
        elseif (jenis_langganan()=="TRADING"){
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'invoice_number' => 'required',
                'due_date'       => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'invoice_number' => 'required',
                'due_date'       => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]);
        }

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('invoices.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        if (jenis_langganan()=="POS"){
            $invoice                 = new Invoice();
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->due_date       = $request->input('invoice_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->paid           = 0;
            $invoice->status         = "Unpaid";
            $invoice->user_id        = user_id();
            $invoice->note           = $request->input('note');
        }
        elseif (jenis_langganan()=="TRADING"){
            $invoice                 = new Invoice();
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->due_date       = $request->input('invoice_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->paid           = 0;
            $invoice->status         = "Unpaid";
            $invoice->user_id        = user_id();
            $invoice->note           = $request->input('note');
        }else{
            $invoice                 = new Invoice();
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->due_date       = $request->input('due_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->paid           = 0;
            $invoice->status         = $request->input('status');
            $invoice->user_id        = user_id();
            $invoice->note           = $request->input('note');    
        }
        $invoice->user_id = user_id();
        $invoice->save();

        $taxes = Tax::all();

        //Save Invoice Item
        for ($i = 0; $i < count($request->product_id); $i++) {
            $invoiceItem              = new InvoiceItem();
            $invoiceItem->invoice_id  = $invoice->id;
            $invoiceItem->item_id     = $request->product_id[$i];
            $invoiceItem->gudang_id   = $request->gudang_id[$i];
            $invoiceItem->description = "";
            $invoiceItem->quantity    = $request->quantity[$i];
            $invoiceItem->unit_cost   = $request->unit_cost[$i];
            $invoiceItem->discount    = $request->discount[$i];
            $invoiceItem->tax_amount  = $request->product_tax[$i];
            $invoiceItem->sub_total   = $request->sub_total[$i];
            $hpp_old = Hpp::where("stok_sisa",">=",$request->quantity[$i])
            ->where("item_id",$request->product_id[$i])->where("gudang_id",$request->gudang_id[$i])
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
           // return $hpp_old;
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai + $invoiceItem->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $invoiceItem->hpp   = $hpp_old->harga;
                $hpp_old->save();
            }
           
            
            $invoiceItem->save();


            $hpp                        = new Hpp();
            $hpp->transaksi_id          = $invoice->id;
            $hpp->transaksi_item_id     = $invoiceItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 6;
            $hpp->stok                  = $hpp->stok + $invoiceItem->quantity;
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();
            
            
            //Store Invoice Taxes
            if (isset($request->tax[$invoiceItem->item_id])) {
                foreach ($request->tax[$invoiceItem->item_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $invoiceItemTax                  = new InvoiceItemTax();
                    $invoiceItemTax->invoice_id      = $invoiceItem->invoice_id;
                    $invoiceItemTax->invoice_item_id = $invoiceItem->id;
                    $invoiceItemTax->tax_id          = $tax->id;
                    $tax_type                        = $tax->type == 'percent' ? '%' : '';
                    $invoiceItemTax->name            = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $invoiceItemTax->amount          = $tax->type == 'percent' ? ($invoiceItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $invoiceItemTax->save();
                }
            }

            //Update Stock if Order Status is received
            if ($request->input('status') != 'Canceled') {
                $stock = Stock::where("product_id", $invoiceItem->item_id)->first();
                if ($stock) {
                    $stock->quantity = $stock->quantity - $invoiceItem->quantity;
                    $stock->save();
                }
            }
        }

        //Increment Invoice Starting number
        if(is_numeric(get_company_option('invoice_starting'))==true){
            increment_invoice_number();
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('invoices.show', $invoice->id)->with('success', _lang('Invoice Created Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Invoice Created Sucessfully'), 'data' => $invoice]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $invoice       = Invoice::find($id);
        $invoice_taxes = InvoiceItemTax::where('invoice_id', $id)
            ->selectRaw('invoice_item_taxes.*, sum(invoice_item_taxes.amount) as tax_amount')
            ->groupBy('invoice_item_taxes.tax_id')
            ->get();
        $transactions = Transaction::where("invoice_id", $id)->get();

        return view('backend.accounting.invoice.view', compact('invoice', 'transactions', 'invoice_taxes', 'id'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $invoice = Invoice::find($id);

        if (!$request->ajax()) {
            return view('backend.accounting.invoice.edit', compact('invoice', 'id'));
        } else {
            return view('backend.accounting.invoice.modal.edit', compact('invoice', 'id'));
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
        if (jenis_langganan()=="POS"){
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]);
        }
        elseif (jenis_langganan()=="TRADING"){
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'due_date'       => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]); 
        }
        else{
            $validator = Validator::make($request->all(), [
                'invoice_number' => 'required|max:191',
                'client_id'      => 'required',
                'cabang_id'      => 'required',
                'invoice_date'   => 'required',
                'due_date'       => 'required',
                'product_id'     => 'required',
            ], [
                'product_id.required' => _lang('You must select at least one product or service'),
            ]);
        }

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('invoices.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();
        if (jenis_langganan()=="POS"){
            $invoice                 = Invoice::find($id);
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->paid           = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->note           = $request->input('note');
            $invoice->user_id_update = user_id();
        }
        if (jenis_langganan()=="TRADING"){
            $invoice                 = Invoice::find($id);
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->due_date       = $request->input('due_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->status         = $request->input('status');
            $invoice->note           = $request->input('note');
            $invoice->user_id_update = user_id();            
        }
        else{
            $invoice                 = Invoice::find($id);
            $invoice->invoice_number = $request->input('invoice_number');
            $invoice->client_id      = $request->input('client_id');
            $invoice->cabang_id      = $request->input('cabang_id');
            $invoice->invoice_date   = $request->input('invoice_date');
            $invoice->due_date       = $request->input('due_date');
            $invoice->grand_total    = $request->product_total + $request->tax_total;
            $invoice->tax_total      = $request->input('tax_total');
            $invoice->status         = $request->input('status');
            $invoice->note           = $request->input('note');
            $invoice->user_id_update = user_id();
        }
        $invoice->user_id_update = user_id();

        $invoice->save();

        $taxes = Tax::all();

        //Update Invoice item
        $invoiceItems = InvoiceItem::where("invoice_id", $id)->get();
        foreach ($invoiceItems as $p_item) {
            $invoiceItem = InvoiceItem::find($p_item->id);
            
            $hpp = Hpp::where("item_id",$p_item->item_id)
            ->where("gudang_id",$p_item->gudang_id)
            ->where("transaksi_item_id",$p_item->id)
            ->orderby("created_at","desc")->first();
            $hpp->delete();
            
            $hpp_old = Hpp::where("item_id",$p_item->item_id)->where("gudang_id",$p_item->gudang_id)
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai - $p_item->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
            
            $invoiceItem->delete();
            update_stock($p_item->item_id);
        }

        $invoiceItemTax = InvoiceItemTax::where("invoice_id", $id);
        $invoiceItemTax->delete();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $invoiceItem              = new InvoiceItem();
            $invoiceItem->invoice_id  = $invoice->id;
            $invoiceItem->item_id     = $request->product_id[$i];
            $invoiceItem->gudang_id     = $request->gudang_id[$i];
            $invoiceItem->description = "";
            $invoiceItem->quantity    = $request->quantity[$i];
            $invoiceItem->unit_cost   = $request->unit_cost[$i];
            $invoiceItem->discount    = $request->discount[$i];
            $invoiceItem->tax_amount  = $request->product_tax[$i];
            $invoiceItem->sub_total   = $request->sub_total[$i];
            
            $hpp_old = Hpp::where("stok_sisa",">=",$request->quantity[$i])
            ->where("item_id",$request->product_id[$i])->where("gudang_id",$request->gudang_id[$i])
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
           // return $hpp_old;
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai + $invoiceItem->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $invoiceItem->hpp   = $hpp_old->harga;
                $hpp_old->save();
            }
            
            $invoiceItem->save();
            
            
            
            $hpp                        = new Hpp();
            $hpp->transaksi_id          = $invoice->id;
            $hpp->transaksi_item_id     = $invoiceItem->id;
            $hpp->item_id               = $request->product_id[$i];
            $hpp->invoice_number        = $request->input('invoice_number');
            $hpp->gudang_id             = $request->gudang_id[$i];
            $hpp->cabang_id             = $request->input('cabang_id');
            $hpp->flag                  = 6;
            $hpp->stok                  = $hpp->stok + $invoiceItem->quantity;
            $hpp->harga                 = $request->sub_total[$i];
            $hpp->save();

            //Store Invoice Taxes
            if (isset($request->tax[$invoiceItem->item_id])) {
                foreach ($request->tax[$invoiceItem->item_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $invoiceItemTax                  = new InvoiceItemTax();
                    $invoiceItemTax->invoice_id      = $invoiceItem->invoice_id;
                    $invoiceItemTax->invoice_item_id = $invoiceItem->id;
                    $invoiceItemTax->tax_id          = $tax->id;
                    $tax_type                        = $tax->type == 'percent' ? '%' : '';
                    $invoiceItemTax->name            = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $invoiceItemTax->amount          = $tax->type == 'percent' ? ($invoiceItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $invoiceItemTax->save();
                }
            }

            update_stock($request->product_id[$i]);
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('invoices.show', $invoice->id)->with('success', _lang('Invoice updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Invoice updated sucessfully'), 'data' => $invoice]);
        }

    }

    /**
     * Generate PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download_pdf($id) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $invoice               = Invoice::find($id);
        $data['invoice']       = $invoice;
        $data['invoice_taxes'] = InvoiceItemTax::where('invoice_id', $id)
            ->selectRaw('invoice_item_taxes.*, sum(invoice_item_taxes.amount) as tax_amount')
            ->groupBy('invoice_item_taxes.tax_id')
            ->get();
        $data['transactions'] = Transaction::where("invoice_id", $id)->get();
        $data['company']      = CompanySetting::where('company_id', $data['invoice']->company_id)->get();

        $pdf = PDF::loadView("backend.accounting.invoice.pdf_export", $data);
        $pdf->setWarnings(false);

        //return $pdf->stream();
        return $pdf->download("invoice_{$invoice->invoice_number}.pdf");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::beginTransaction();

        $invoice = Invoice::find($id);

        if ($invoice->quotation_id !=""){
            $quotation = Quotation::find($invoice->quotation_id,'id');
            $quotation->status="0";
            $quotation->save();
               
        }

        $invoice->delete();

        $transaksi = Transaction::where("invoice_id",$id);
        if ($transaksi){
            $transaksi->delete();
        }

        $invoiceItems = InvoiceItem::where("invoice_id", $id)->get();
        foreach ($invoiceItems as $p_item) {
            $invoiceItem = InvoiceItem::find($p_item->id);
            
            $hpp = Hpp::where("item_id",$p_item->item_id)
            ->where("gudang_id",$p_item->gudang_id)
            ->where("transaksi_item_id",$p_item->id)
            ->orderby("created_at","desc")->first();
            $hpp->delete();
            
            $hpp_old = Hpp::where("item_id",$p_item->item_id)->where("gudang_id",$p_item->gudang_id)
            ->where("flag","<=",5)
            ->orderby("created_at","desc")->first();
            if ($hpp_old) {
                $hpp_old->stok_terpakai = $hpp_old->stok_terpakai - $p_item->quantity;
                $hpp_old->stok_sisa = $hpp_old->stok-$hpp_old->stok_terpakai; 
                $hpp_old->save();
            }
            
            $invoiceItem->delete();
            update_stock($p_item->item_id);
        }

        $invoiceItemTax = InvoiceItemTax::where('invoice_id', $id);
        $invoiceItemTax->delete();

        DB::commit();

        return redirect()->route('invoices.index')->with('success', _lang('Invoice deleted sucessfully'));
    }

    public function store_payment(Request $request, $id = "") {
        if ($request->isMethod('get')) {
            $invoice = Invoice::find($id);

            if ($request->ajax()) {
                return view('backend.accounting.invoice.modal.create_payment', compact('invoice', 'id'));
            }
        } else if ($request->isMethod('post')) {
            if(jenis_langganan()=="POS" || jenis_langganan()=="TRADING"){
                $validator = Validator::make($request->all(), [
                    'invoice_id'        => 'required',
                    'amount'            => 'required|numeric',
                    'payment_method_id' => 'required',
                    'reference'         => 'nullable|max:50',
                    'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'invoice_id'        => 'required',
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

            $invoice = Invoice::find($request->invoice_id);
            if ($invoice->status == 'Paid') {
                return back()->with('error', _lang('Invoice is already paid !'));
            }

            $attachment = "";
            if ($request->hasfile('attachment')) {
                $file       = $request->file('attachment');
                $attachment = time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/transactions/", $attachment);
            }

            if (jenis_langganan()=="POS"){
                $transaction                    = new Transaction();
                $transaction->trans_date        = date('Y-m-d');
                $transaction->type              = 'income';
                $transaction->dr_cr             = 'cr';
                $transaction->amount            = $request->input('amount');
                $transaction->payer_payee_id    = $request->input('client_id');
                $transaction->payment_method_id = $request->input('payment_method_id');
                $transaction->invoice_id        = $request->input('invoice_id');
                $transaction->reference         = $request->input('reference');
                $transaction->note              = $request->input('note');
                $transaction->attachment        = $attachment;
                $transaction->user_id           = user_id();
            }
            elseif (jenis_langganan()=="TRADING"){
                $transaction                    = new Transaction();
                $transaction->trans_date        = date('Y-m-d');
                $transaction->type              = 'income';
                $transaction->dr_cr             = 'cr';
                $transaction->amount            = $request->input('amount');
                $transaction->payer_payee_id    = $request->input('client_id');
                $transaction->payment_method_id = $request->input('payment_method_id');
                $transaction->invoice_id        = $request->input('invoice_id');
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
                $transaction->payer_payee_id    = $request->input('client_id');
                $transaction->payment_method_id = $request->input('payment_method_id');
                $transaction->invoice_id        = $request->input('invoice_id');
                $transaction->reference         = $request->input('reference');
                $transaction->note              = $request->input('note');
                $transaction->attachment        = $attachment;
                $transaction->user_id           = user_id();
            }
            $transaction->user_id        = user_id();
            $transaction->save();

            //Update Invoice Table
            $invoice->paid = $invoice->paid + $transaction->amount;
            if ($invoice->paid >= $invoice->grand_total) {
                $invoice->status = 'Paid';
            } else if ($invoice->paid > 0 && ($invoice->paid < $invoice->grand_total)) {
                $invoice->status = 'Partially_Paid';
            }
            $invoice->save();

            if ($request->ajax()) {
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment was made Sucessfully'), 'data' => $transaction]);
            }
        }
    }

    public function view_payment(Request $request, $invoice_id) {

        $transactions = Transaction::where('invoice_id', $invoice_id)->get();

        if (!$request->ajax()) {
            return view('backend.accounting.invoice.view_payment', compact('transactions'));
        } else {
            return view('backend.accounting.invoice.modal.view_payment', compact('transactions'));
        }
    }

    public function send_email(Request $request, $invoice_id = '') {
        if ($request->isMethod('get')) {
            $invoice = Invoice::find($invoice_id);

            $client_email = $invoice->client->contact_email;

            if ($request->ajax()) {
                return view('backend.accounting.invoice.modal.send_email', compact('client_email', 'invoice'));
            }
            return back();
        } else if ($request->isMethod('post')) {

            @ini_set('max_execution_time', 0);
            @set_time_limit(0);
            Overrider::load("Settings");

            $validator = Validator::make($request->all(), [
                'email_subject' => 'required',
                'email_message' => 'required',
                'contact_email' => 'required',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {
                    return back()->withErrors($validator)
                        ->withInput();
                }
            }

            //Send email
            $subject       = $request->input("email_subject");
            $message       = $request->input("email_message");
            $contact_email = $request->input("contact_email");

            $contact = \App\Contact::where('contact_email', $contact_email)->first();
            $invoice = Invoice::find($request->invoice_id);

            $currency = currency();

            if ($contact) {
                //Replace Paremeter
                $replace = array(
                    '{customer_name}'  => $contact->contact_name,
                    '{invoice_no}'     => $invoice->invoice_number,
                    '{invoice_date}'   => $invoice->invoice_date,
                    '{due_date}'       => $invoice->due_date,
                    '{payment_status}' => _dlang(str_replace('_', ' ', $invoice->status)),
                    '{grand_total}'    => decimalPlace($invoice->grand_total, $currency),
                    '{amount_due}'     => decimalPlace(($invoice->grand_total - $invoice->paid), $currency),
                    '{total_paid}'     => decimalPlace($invoice->paid, $currency),
                    '{invoice_link}'   => route('client.view_invoice', encrypt($invoice->id)),
                );

            }

            $mail          = new \stdClass();
            $mail->subject = $subject;
            $mail->body    = process_string($replace, $message);

            try {
                Mail::to($contact_email)->send(new GeneralMail($mail));
            } catch (\Exception$e) {
                dd($e);
                if (!$request->ajax()) {
                    return back()->with('error', _lang('Sorry, Error Occured !'));
                } else {
                    return response()->json(['result' => 'error', 'message' => _lang('Sorry, Error Occured !')]);
                }
            }

            if (!$request->ajax()) {
                return back()->with('success', _lang('Email Send Sucessfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Email Send Sucessfully'), 'data' => $contact]);
            }
        }
    }

}