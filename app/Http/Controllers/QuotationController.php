<?php

namespace App\Http\Controllers;

use App\CompanySetting;
use App\Invoice;
use App\InvoiceItem;
use App\InvoiceItemTax;
use App\Mail\GeneralMail;
use App\Quotation;
use App\QuotationItem;
use App\QuotationItemTax;
use App\Stock;
use App\Hpp;
use App\Tax;
use App\Utilities\Overrider;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PDF;
use Validator;

class QuotationController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.accounting.quotation.list');
    }

    public function get_table_data() {

        $currency = currency();

        $quotations = Quotation::with("client")
            ->select('quotations.*')
            ->orderBy("quotations.id", "desc");

        return Datatables::eloquent($quotations)
            ->editColumn('quotation_number', function ($quotation) {
                if ($quotation->status == 0) {
                    return $quotation->quotation_number;
                } else {
                    return $quotation->quotation_number . "<a href='" . route('invoices.show', $quotation->invoice_id) . "'><small class='badge badge-secondary float-right'>" . _lang('Converted') . "</small></a>";
                }

            })
            ->editColumn('grand_total', function ($quotation) use ($currency) {
                return "<span class='float-right'>" . decimalPlace($quotation->grand_total, $currency) . "</span>";
            })
            ->addColumn('action', function ($quotation) {
                if ($quotation->status==0){
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '<i class="mdi mdi-chevron-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('QuotationController@edit', $quotation->id) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a></li>'
                    . '<a class="dropdown-item" href="' . action('QuotationController@show', $quotation->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
                    . '<a class="dropdown-item" href="' . action('QuotationController@convert_invoice', $quotation->id) . '"><i class="ti-exchange-vertical"></i> ' . _lang('Convert to Invoice') . '</a></li>'
                    . '<form action="' . action('QuotationController@destroy', $quotation['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="button-link btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                        . '</form>'
                        . '</div>'
                        . '</div>';
                }
                else{
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '<i class="mdi mdi-chevron-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . action('QuotationController@show', $quotation->id) . '"><i class="ti-eye"></i> ' . _lang('View') . '</a></li>'
                    . '</div>'
                    . '</div>';
                }
            })
            ->setRowId(function ($invoice) {
                return "row_" . $invoice->id;
            })
            ->rawColumns(['quotation_number', 'grand_total', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.quotation.create');
        } else {
            return view('backend.accounting.quotation.modal.create');
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
            'quotation_number' => 'required|max:191',
            'client_id'        => 'required',
            'cabang_id'        => 'required',
            'quotation_date'   => 'required',
            'product_id'       => 'required',
        ], [
            'product_id.required' => _lang('You must select at least one product or service'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('quotations.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        $quotation                   = new Quotation();
        $quotation->cabang_id        = $request->input('cabang_id');
        $quotation->quotation_number = $request->input('quotation_number');
        $quotation->client_id        = $request->input('client_id');
        $quotation->quotation_date   = $request->input('quotation_date');
        $quotation->grand_total      = $request->product_total + $request->tax_total;
        $quotation->tax_total        = $request->input('tax_total');
        $quotation->note             = $request->input('note');
        $quotation->status           = 0;
        $quotation->user_id          = user_id();

        $quotation->save();

        $taxes = Tax::all();

        //Save quotation Item
        for ($i = 0; $i < count($request->product_id); $i++) {
            $quotationItem               = new quotationItem();
            $quotationItem->quotation_id = $quotation->id;
            $quotationItem->item_id      = $request->product_id[$i];
            $quotationItem->gudang_id    = $request->gudang_id[$i];
            $quotationItem->description  = "";
            $quotationItem->quantity     = $request->quantity[$i];
            $quotationItem->unit_cost    = $request->unit_cost[$i];
            $quotationItem->discount     = $request->discount[$i];
            $quotationItem->tax_amount   = $request->product_tax[$i];
            $quotationItem->sub_total    = $request->sub_total[$i];
            $quotationItem->save();

            //Store Quotation Taxes
            if (isset($request->tax[$quotationItem->item_id])) {
                foreach ($request->tax[$quotationItem->item_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $quotationItemTax                    = new QuotationItemTax();
                    $quotationItemTax->quotation_id      = $quotationItem->quotation_id;
                    $quotationItemTax->quotation_item_id = $quotationItem->id;
                    $quotationItemTax->tax_id            = $tax->id;
                    $tax_type                            = $tax->type == 'percent' ? '%' : '';
                    $quotationItemTax->name              = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $quotationItemTax->amount            = $tax->type == 'percent' ? ($quotationItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $quotationItemTax->save();
                }
            }

        }

        //Increment quotation Starting number
        if(is_numeric(get_company_option('quotation_starting'))==true){
            increment_quotation_number();
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('quotations.show', $quotation->id)->with('success', _lang('Quotation Created Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Quotation Created Sucessfully'), 'data' => $quotation]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $quotation       = Quotation::find($id);
        $quotation_taxes = QuotationItemTax::where('quotation_id', $id)
            ->selectRaw('quotation_item_taxes.*,sum(quotation_item_taxes.amount) as tax_amount')
            ->groupBy('quotation_item_taxes.tax_id')
            ->get();

        return view('backend.accounting.quotation.view', compact('quotation', 'quotation_taxes', 'id'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $quotation = Quotation::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.quotation.edit', compact('quotation', 'id'));
        } else {
            return view('backend.accounting.quotation.modal.edit', compact('quotation', 'id'));
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
            'quotation_number' => 'required|max:191',
            'client_id'        => 'required',
            'cabang_id'        => 'required',
            'quotation_date'   => 'required',
            'product_id'       => 'required',
        ], [
            'product_id.required' => _lang('You must select at least one product or service'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('quotations.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        $quotation                   = Quotation::find($id);
        $quotation->quotation_number = $request->input('quotation_number');
        $quotation->client_id        = $request->input('client_id');
        $quotation->cabang_id        = $request->input('cabang_id');
        $quotation->quotation_date   = $request->input('quotation_date');
        $quotation->grand_total      = $request->product_total + $request->tax_total;
        $quotation->tax_total        = $request->input('tax_total');
        $quotation->note             = $request->input('note');
        $quotation->user_id_update   = user_id();

        $quotation->save();

        $taxes = Tax::all();

        //Update quotation item
        $quotationItem = QuotationItem::where("quotation_id", $id);
        $quotationItem->delete();

        $quotationItemTax = QuotationItemTax::where("quotation_id", $id);
        $quotationItemTax->delete();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $quotationItem               = new quotationItem();
            $quotationItem->quotation_id = $quotation->id;
            $quotationItem->item_id      = $request->product_id[$i];
            $quotationItem->gudang_id    = $request->gudang_id[$i];
			$quotationItem->description  = "";
            $quotationItem->quantity     = $request->quantity[$i];
            $quotationItem->unit_cost    = $request->unit_cost[$i];
            $quotationItem->discount     = $request->discount[$i];
            $quotationItem->tax_amount   = $request->product_tax[$i];
            $quotationItem->sub_total    = $request->sub_total[$i];
            $quotationItem->save();

            //Store Quotation Taxes
            if (isset($request->tax[$quotationItem->item_id])) {
                foreach ($request->tax[$quotationItem->item_id] as $taxId) {
                    $tax = $taxes->firstWhere('id', $taxId);

                    $quotationItemTax                    = new QuotationItemTax();
                    $quotationItemTax->quotation_id      = $quotationItem->quotation_id;
                    $quotationItemTax->quotation_item_id = $quotationItem->id;
                    $quotationItemTax->tax_id            = $tax->id;
                    $tax_type                            = $tax->type == 'percent' ? '%' : '';
                    $quotationItemTax->name              = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                    $quotationItemTax->amount            = $tax->type == 'percent' ? ($quotationItem->sub_total / 100) * $tax->rate : $tax->rate;
                    $quotationItemTax->save();
                }
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('quotations.show', $quotation->id)->with('success', _lang('Quotation updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Quotation updated sucessfully'), 'data' => $quotation]);
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

        $quotation               = Quotation::find($id);
        $data['quotation']       = $quotation;
        $data['quotation_taxes'] = QuotationItemTax::where('quotation_id', $id)
            ->selectRaw('quotation_item_taxes.*,sum(quotation_item_taxes.amount) as tax_amount')
            ->groupBy('quotation_item_taxes.tax_id')
            ->get();
        $data['company'] = CompanySetting::where('company_id', $data['quotation']->company_id)->get();

        $pdf = PDF::loadView("backend.accounting.quotation.pdf_export", $data);
        $pdf->setWarnings(false);

        //return $pdf->stream();
        return $pdf->download("quotation_{$quotation->quotation_number}.pdf");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::beginTransaction();

        $quotation = Quotation::find($id);
        $quotation->delete();

        $quotationItem = QuotationItem::where("quotation_id", $id);
        $quotationItem->delete();

        $quotationItemTax = QuotationItemTax::where('quotation_id', $id);
        $quotationItemTax->delete();

        DB::commit();

        return redirect()->route('quotations.index')->with('success', _lang('Quotation Removed Sucessfully'));
    }

    public function convert_invoice($quotation_id) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        DB::beginTransaction();

        $quotation = Quotation::where('id', $quotation_id)->where('status', 0)->first();

        if (!$quotation) {
            return back()->with('error', _lang('Sorry, Quotation is already converted to Invoice !'));
        }

        $invoice                 = new Invoice();
        $invoice->invoice_number = get_company_option('invoice_prefix') . get_company_option('invoice_starting');
        $invoice->client_id      = $quotation->client_id;
        $invoice->cabang_id      = $quotation->cabang_id;
        $invoice->invoice_date   = date('Y-m-d');
        $invoice->due_date       = date('Y-m-d');
        $invoice->grand_total    = $quotation->grand_total;
        $invoice->tax_total      = $quotation->tax_total;
        $invoice->paid           = 0;
        $invoice->status         = 'Unpaid';
        $invoice->quotation_id   = $quotation->id;
        $invoice->note           = $quotation->note;
        $invoice->user_id        = user_id();

        $invoice->save();

        $taxes = Tax::all();

        //Save Invoice Item
        foreach ($quotation->quotation_items as $quotation_item) {
            $invoiceItem              = new InvoiceItem();
            $invoiceItem->invoice_id  = $invoice->id;
            $invoiceItem->description = $quotation_item->description;
            $invoiceItem->gudang_id   = $quotation_item->gudang_id;
            $invoiceItem->item_id     = $quotation_item->item_id;
            $invoiceItem->quantity    = $quotation_item->quantity;
            $invoiceItem->unit_cost   = $quotation_item->unit_cost;
            $invoiceItem->discount    = $quotation_item->discount;
            $invoiceItem->tax_amount  = $quotation_item->tax_amount;
            $invoiceItem->sub_total   = $quotation_item->sub_total;
            $hpp_old = Hpp::where("stok_sisa",">=",$quotation_item->quantity)
            ->where("item_id",$quotation_item->item_id)->where("gudang_id",$quotation_item->gudang_id)
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
            $hpp->item_id               = $quotation_item->item_id;
            $hpp->invoice_number        = get_company_option('invoice_prefix') . get_company_option('invoice_starting');
            $hpp->gudang_id             = $quotation_item->gudang_id;
            $hpp->cabang_id             = $quotation->cabang_id;
            $hpp->flag                  = 6;
            $hpp->stok                  = $hpp->stok + $invoiceItem->quantity;
            $hpp->harga                 = $quotation_item->sub_total;
            $hpp->save();

            //Store Invoice Taxes
            foreach ($quotation_item->taxes as $quotation_tax) {

                $tax = $taxes->firstWhere('id', $quotation_tax->tax_id);

                $invoiceItemTax                  = new InvoiceItemTax();
                $invoiceItemTax->invoice_id      = $invoiceItem->invoice_id;
                $invoiceItemTax->invoice_item_id = $invoiceItem->id;
                $invoiceItemTax->tax_id          = $tax->id;
                $tax_type                        = ($tax->type == 'percent' ? '%' : '');
                $invoiceItemTax->name            = $tax->tax_name . ' @ ' . $tax->rate . $tax_type;
                $invoiceItemTax->amount          = $tax->type == 'percent' ? ($invoiceItem->sub_total / 100) * $tax->rate : $tax->rate;
                $invoiceItemTax->save();
            }

            //Update Stock
            $stock = Stock::where("product_id", $invoiceItem->item_id)->first();
            if (!empty($stock)) {
                $stock->quantity = $stock->quantity - $invoiceItem->quantity;
                $stock->save();
            }

        }
        //Increment Invoice Starting number
        if(is_numeric(get_company_option('invoice_starting'))==true){
            increment_invoice_number();
        }

        $quotation->status     = 1;
        $quotation->invoice_id = $invoice->id;
        $quotation->save();

        DB::commit();

        return redirect('invoices/' . $invoice->id)->with('success', _lang('Quotation Converted Sucessfully'));

    }

    public function send_email(Request $request, $quotation_id = '') {
        if ($request->isMethod('get')) {
            $quotation = Quotation::find($quotation_id);

            $client_email = $quotation->client->contact_email;

            if ($request->ajax()) {
                return view('backend.accounting.quotation.modal.send_email', compact('client_email', 'quotation'));
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

            $currency = currency();

            $contact   = \App\Contact::where('contact_email', $contact_email)->first();
            $quotation = Quotation::find($request->quotation_id);

            if ($contact) {
                //Replace Paremeter
                $replace = array(
                    '{customer_name}'  => $contact->contact_name,
                    '{quotation_no}'   => $quotation->quotation_number,
                    '{quotation_date}' => $quotation->quotation_date,
                    '{grand_total}'    => decimalPlace($quotation->grand_total, $currency),
                    '{quotation_link}' => route('client.view_quotation', encrypt($quotation->id)),
                );
            }

            $mail          = new \stdClass();
            $mail->subject = $subject;
            $mail->body    = process_string($replace, $message);

            try {
                Mail::to($contact_email)->send(new GeneralMail($mail));
            } catch (\Exception $e) {
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