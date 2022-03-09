<?php

namespace App\Http\Controllers;

use App\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Validator;
use DB;

class IncomeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.accounting.income.list');
    }

    public function get_table_data() {

        $currency = currency();

        if (jenis_langganan()=="POS"){
            $transactions = Transaction::with("income_type")
                ->with("payer")
                ->with("payment_method")
                ->select('transactions.*')
                ->where("transactions.dr_cr", "cr")
                ->orderBy("transactions.id", "desc");
        }
        elseif(jenis_langganan()=="TRADING"){
            $transactions = Transaction::with("income_type")
                ->with("payer")
                ->with("payment_method")
                ->select('transactions.*')
                ->where("transactions.dr_cr", "cr")
                ->orderBy("transactions.id", "desc");
        }
        else{
            $transactions = Transaction::with("account")
                ->with("income_type")
                ->with("payer")
                ->with("payment_method")
                ->select('transactions.*')
                ->where("transactions.dr_cr", "cr")
                ->orderBy("transactions.id", "desc");
        }
        return Datatables::eloquent($transactions)  
            ->editColumn('amount', function ($trans) use ($currency) {
                return "<span class='float-right'>" .decimalPlace($trans->amount, $currency) . "</span>";
            })
            ->editColumn('payer.contact_name', function ($trans) {
                return isset($trans->payer->contact_name) ? $trans->payer->contact_name : '';
            })
            ->editColumn('income_type.name', function ($trans) {
                return isset($trans->income_type->name) ? $trans->income_type->name : _lang('Transfer');
            })
            ->addColumn('action', function ($trans) {
                if (jenis_langganan()=="POS")
                {
                    return '<form action="' . action('IncomeController@destroy', $trans['id']) . '" class="text-center" method="post">'
                    . '<a href="' . action('IncomeController@edit', $trans['id']) . '" data-title="' . _lang('Update Income') . '" class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a> '
                    . '<a href="' . action('IncomeController@show', $trans['id']) . '" data-title="' . _lang('View Income') . '" class="btn btn-info btn-sm ajax-modal"><i class="ti-eye"></i></a> '
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="btn btn-danger btn-sm btn-remove" type="submit"><i class="ti-trash"></i></button>'
                        . '</form>';
                }
                elseif(jenis_langganan()=="TRADING")
                {
                    if (isset($trans->income_type->name)) {
                        return '<form action="' . action('IncomeController@destroy', $trans['id']) . '" class="text-center" method="post">'
                        . '<a href="' . action('IncomeController@edit', $trans['id']) . '" data-title="' . _lang('Update Income') . '" class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a> '
                        . '<a href="' . action('IncomeController@show', $trans['id']) . '" data-title="' . _lang('View Income') . '" class="btn btn-info btn-sm ajax-modal"><i class="ti-eye"></i></a> '
                        . csrf_field()
                        . '<input name="_method" type="hidden" value="DELETE">'
                        . '<button class="btn btn-danger btn-sm btn-remove" type="submit"><i class="ti-trash"></i></button>'
                            . '</form>';
                    } else {
                        return '<form action="' . action('IncomeController@destroy', $trans['id']) . '" class="text-center" method="post">'
                        . '<a href="#" data-title="' . _lang('Update Income') . '" class="btn btn-warning btn-sm disabled"><i class="ti-pencil-alt"></i></a> '
                        . '<a href="' . action('IncomeController@show', $trans['id']) . '" data-title="' . _lang('View Income') . '" class="btn btn-info btn-sm ajax-modal"><i class="ti-eye"></i></a> '
                        . csrf_field()
                        . '<input name="_method" type="hidden" value="DELETE">'
                        . '<button class="btn btn-danger btn-sm btn-remove" type="submit"><i class="ti-trash"></i></button>'
                            . '</form>';
                    }
                }
                else{
                    if (isset($trans->income_type->name)) {
                        return '<form action="' . action('IncomeController@destroy', $trans['id']) . '" class="text-center" method="post">'
                        . '<a href="' . action('IncomeController@edit', $trans['id']) . '" data-title="' . _lang('Update Income') . '" class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a> '
                        . '<a href="' . action('IncomeController@show', $trans['id']) . '" data-title="' . _lang('View Income') . '" class="btn btn-info btn-sm ajax-modal"><i class="ti-eye"></i></a> '
                        . csrf_field()
                        . '<input name="_method" type="hidden" value="DELETE">'
                        . '<button class="btn btn-danger btn-sm btn-remove" type="submit"><i class="ti-trash"></i></button>'
                            . '</form>';
                    } else {
                        return '<form action="' . action('IncomeController@destroy', $trans['id']) . '" class="text-center" method="post">'
                        . '<a href="#" data-title="' . _lang('Update Income') . '" class="btn btn-warning btn-sm disabled"><i class="ti-pencil-alt"></i></a> '
                        . '<a href="' . action('IncomeController@show', $trans['id']) . '" data-title="' . _lang('View Income') . '" class="btn btn-info btn-sm ajax-modal"><i class="ti-eye"></i></a> '
                        . csrf_field()
                        . '<input name="_method" type="hidden" value="DELETE">'
                        . '<button class="btn btn-danger btn-sm btn-remove" type="submit"><i class="ti-trash"></i></button>'
                            . '</form>';
                    }
                }
            })
            ->setRowId(function ($trans) {
                return "row_" . $trans->id;
            })
            ->rawColumns(['status', 'action', 'amount'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.income.create');
        } else {
            return view('backend.accounting.income.modal.create');
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
            'trans_date'        => 'required',
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
                return redirect()->route('income.create')
                    ->withErrors($validator)
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
        $transaction->trans_date        = $request->input('trans_date');
        $transaction->account_id        = $request->input('account_id');
        $transaction->chart_id          = $request->input('chart_id');
        $transaction->type              = 'income';
        $transaction->dr_cr             = 'cr';
        $transaction->amount            = $request->input('amount');
        $transaction->payer_payee_id    = $request->input('payer_payee_id');
        $transaction->payment_method_id = $request->input('payment_method_id');
        $transaction->reference         = $request->input('reference');
        $transaction->note              = $request->input('note');
        $transaction->attachment        = $attachment;

        $transaction->save();

        //Set Related Data
        $transaction->amount            = decimalPlace($transaction->amount, currency());
        $transaction->account_id        = $transaction->account->account_title;
        $transaction->chart_id          = $transaction->income_type->name;
        $transaction->payer_payee_id    = isset($transaction->payer->contact_name) ? $transaction->payer->contact_name : '';
        $transaction->payment_method_id = $transaction->payment_method->name;

        if (!$request->ajax()) {
            return redirect()->route('income.index')->with('success', _lang('Saved Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Sucessfully'), 'data' => $transaction]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.income.view', compact('transaction', 'id'));
        } else {
            return view('backend.accounting.income.modal.view', compact('transaction', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.income.edit', compact('transaction', 'id'));
        } else {
            return view('backend.accounting.income.modal.edit', compact('transaction', 'id'));
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
        if (jenis_langganan()=="POS" || jenis_langganan()=="TRADING")
        {
            $validator = Validator::make($request->all(), [
                'trans_date'        => 'required',
                'amount'            => 'required|numeric',
                'payment_method_id' => 'required',
                'reference'         => 'nullable|max:50',
                'attachment'        => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
            ]);
        }
        else{
            $validator = Validator::make($request->all(), [
                'trans_date'        => 'required',
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
                return redirect()->route('income.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = "";
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/transactions/", $attachment);
        }

        $transaction                    = Transaction::find($id);
        $transaction->trans_date        = $request->input('trans_date');
        $transaction->account_id        = $request->input('account_id');
        $transaction->chart_id          = $request->input('chart_id');
        $transaction->type              = 'income';
        $transaction->dr_cr             = 'cr';
        $transaction->amount            = $request->input('amount');
        $transaction->payer_payee_id    = $request->input('payer_payee_id');
        $transaction->payment_method_id = $request->input('payment_method_id');
        $transaction->reference         = $request->input('reference');
        $transaction->note              = $request->input('note');
        if ($request->hasfile('attachment')) {
            $transaction->attachment = $attachment;
        }

        $transaction->save();

        //Set Related Data
        $transaction->amount            = decimalPlace($transaction->amount, currency());
        $transaction->account_id        = $transaction->account->account_title;
        $transaction->chart_id          = $transaction->income_type->name;
        $transaction->payer_payee_id    = isset($transaction->payer->contact_name) ? $transaction->payer->contact_name : '';
        $transaction->payment_method_id = $transaction->payment_method->name;
        

        if ($transaction->invoice_id!=="" && $transaction->invoice_id!==NULL){
            $jmlbayar = 0;
            $totalbayar =  DB::select("SELECT sum(amount) as amount FROM transactions WHERE invoice_id=". $transaction->invoice_id); 
            
            if (!empty($totalbayar[0]->amount)){
                $jmlbayar=$totalbayar[0]->amount;
            }
            DB::select("UPDATE invoices set paid=". $jmlbayar ." WHERE id=".$transaction->invoice_id);
            DB::select("UPDATE invoices set status='paid' WHERE grand_total<=paid AND id=".$transaction->invoice_id);
            DB::select("UPDATE invoices set status='Unpaid' WHERE grand_total>paid AND id=".$transaction->invoice_id);
        }

        if ($transaction->purchase_return_id!=="" && $transaction->purchase_return_id!==NULL){
            $jmlbayar = 0;
            $totalbayar =  DB::select("SELECT sum(amount) as amount FROM transactions WHERE purchase_return_id=". $transaction->purchase_return_id); 
            if (!empty($totalbayar[0]->amount)){
                $jmlbayar=$totalbayar[0]->amount;
            }
            DB::select("UPDATE purchase_return set paid=". $jmlbayar ." WHERE id=".$transaction->purchase_return_id);
            DB::select("UPDATE purchase_return set payment_status=1 WHERE grand_total<=paid AND id=".$transaction->purchase_return_id);
            DB::select("UPDATE purchase_return set payment_status=0 WHERE grand_total>paid AND id=".$transaction->purchase_return_id);
        }

        
        if (!$request->ajax()) {
            return redirect()->route('income.index')->with('success', _lang('Updated Sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Sucessfully'), 'data' => $transaction]);
        }

    }

    public function calendar() {
        $transactions = Transaction::where("type", "income")
            ->orderBy("id", "desc")->get();
        return view('backend.accounting.income.calendar', compact('transactions'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $transaction = Transaction::find($id);
        $transaction->delete();

        if ($transaction->invoice_id!=="" && $transaction->invoice_id!==NULL){
            $jmlbayar = 0;
            $totalbayar =  DB::select("SELECT sum(amount) as amount FROM transactions WHERE invoice_id=". $transaction->invoice_id); 
            
            if (!empty($totalbayar[0]->amount)){
                $jmlbayar=$totalbayar[0]->amount;
            }
            DB::select("UPDATE invoices set paid=". $jmlbayar ." WHERE id=".$transaction->invoice_id);
            DB::select("UPDATE invoices set status='paid' WHERE grand_total<=paid AND id=".$transaction->invoice_id);
            DB::select("UPDATE invoices set status='Unpaid' WHERE grand_total>paid AND id=".$transaction->invoice_id);
        }

        if ($transaction->purchase_return_id!=="" && $transaction->purchase_return_id!==NULL){
            $jmlbayar = 0;
            $totalbayar =  DB::select("SELECT sum(amount) as amount FROM transactions WHERE purchase_return_id=". $transaction->purchase_return_id); 
            if (!empty($totalbayar[0]->amount)){
                $jmlbayar=$totalbayar[0]->amount;
            }
            DB::select("UPDATE purchase_return set paid=". $jmlbayar ." WHERE id=".$transaction->purchase_return_id);
            DB::select("UPDATE purchase_return set payment_status=1 WHERE grand_total<=paid AND id=".$transaction->purchase_return_id);
            DB::select("UPDATE purchase_return set payment_status=0 WHERE grand_total>paid AND id=".$transaction->purchase_return_id);
        }

        return back()->with('success', _lang('Removed Sucessfully'));
    }
}
