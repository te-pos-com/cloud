<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Transaction;
use Auth;
use DB;

class DashboardController extends Controller {

    public function __construct()
    {
        date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));  
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $type = Auth::user()->user_type;
        if ($type == "admin") {
            $data               = array();
            $data['total_user'] = \App\User::selectRaw('COUNT(id) as c')
                ->where('user_type', 'user')
                ->first()->c;
            $data['trail_user'] = \App\User::selectRaw('COUNT(id) as c')
                ->where('user_type', 'user')
                ->where('membership_type', 'trial')
                ->first()->c;
            $data['paid_user'] = \App\User::selectRaw('COUNT(id) as c')
                ->where('user_type', 'user')
                ->where('membership_type', 'member')
                ->first()->c;
            $data['total_payment'] = \App\PaymentHistory::selectRaw('SUM(amount) as total')
                ->where('status', 'paid')
                ->first()->total;
            $data['recent_users'] = \App\User::where("user_type", "user")
                ->orderBy("id", "desc")
                ->limit(5)->get();

            $data['recent_payments'] = \App\PaymentHistory::where('status', 'paid')
                ->limit(5)->get();

            return view('backend/dashboard-' . $type, $data);
        } else if ($type == "client") {
            $client_id        = Auth::user()->client->id;
            $data             = array();
            $data['invoices'] = Invoice::where('client_id', $client_id)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            $data['transactions'] = Transaction::where('payer_payee_id', $client_id)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            return view('backend/dashboard-' . $type, $data);
        } else {
            $data                          = array();
            $data['current_day_income']    = current_day_income();
            $data['current_day_expense']   = current_day_expense();
            $data['current_month_income']  = current_month_income();
            $data['current_month_expense'] = current_month_expense();
            $data['latest_income']         = Transaction::where("company_id", company_id())
                ->where("dr_cr", "cr")
                ->orderBy("id", "desc")->limit(5)->get();

            $data['income_user']         = DB::select("SELECT u.name,sum(t.amount) as amount from transactions t 
            INNER JOIN users u ON u.id=t.user_id where t.company_id=". company_id() ." and t.dr_cr='cr' order by amount desc LIMIT 5");

            $data['income_produk']         = DB::select("SELECT i.item_name,sum(t.amount) as amount from transactions t
            INNER JOIN invoice_items id on t.invoice_id =  id.invoice_id
            INNER JOIN items i ON i.id=id.item_id where t.company_id=". company_id() ." and t.dr_cr='cr' LIMIT 5");

            $data['latest_expense'] = Transaction::where("company_id", company_id())
                ->where("dr_cr", "dr")
                ->orderBy("id", "desc")->limit(5)->get();
            if (jenis_langganan()=="POS"){
                return view('backend/dashboard_pos-' . $type, $data);
            }else{
                return view('backend/dashboard-' . $type, $data);
            }
        }
    }

    public function json_month_wise_income_expense() {
        $income  = $this->get_month_wise_income();
        $expense = $this->get_month_wise_expense();

        $months         = '"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"';
        $income_string  = '';
        $expense_string = '';

        foreach ($income as $i) {
            $income_string = $income_string . $i->amount . ",";
        }

        $income_string = rtrim($income_string, ",");

        foreach ($expense as $e) {
            $expense_string = $expense_string . $e->amount . ",";
        }
        $expense_string = rtrim($expense_string, ",");

        echo '{"Months":[' . $months . '], "Income":[' . $income_string . '], "Expense":[' . $expense_string . ']}';
        exit();
    }

    public function json_income_vs_expense() {
        $income  = $this->get_current_month_income();
        $expense = $this->get_current_month_expense();
        echo '{"Income":[' . $income . '], "Expense":[' . $expense . ']}';
        exit();
    }

    private function get_month_wise_income() {
        $company_id = company_id();
        $date       = date("Y-m-d");
        $query      = DB::select("SELECT m.month, IFNULL(SUM(transactions.amount),0) as amount
		FROM ( SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION SELECT 4 AS MONTH
		UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH UNION SELECT 7 AS MONTH UNION SELECT 8 AS MONTH
		UNION SELECT 9 AS MONTH UNION SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH ) AS m
		LEFT JOIN transactions ON m.month = MONTH(trans_date) AND YEAR(transactions.trans_date)=YEAR('$date')
		AND dr_cr='cr' AND company_id='$company_id' GROUP BY m.month ORDER BY m.month ASC");
        return $query;
    }

    private function get_month_wise_expense() {
        $company_id = company_id();
        $date       = date("Y-m-d");
        $query      = DB::select("SELECT m.month, IFNULL(SUM(transactions.amount),0) as amount
		FROM ( SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION SELECT 4 AS MONTH
		UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH UNION SELECT 7 AS MONTH UNION SELECT 8 AS MONTH
		UNION SELECT 9 AS MONTH UNION SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH ) AS m
		LEFT JOIN transactions ON m.month = MONTH(trans_date) AND YEAR(transactions.trans_date)=YEAR('$date')
		AND dr_cr='dr' AND company_id='$company_id' GROUP BY m.month ORDER BY m.month ASC");
        return $query;
    }

    private function get_current_month_income() {
        $company_id = company_id();
        $date       = date("Y-m-d");
        $query      = DB::select("SELECT IFNULL(SUM(amount),0) as amount FROM transactions WHERE dr_cr='cr'
	 AND trans_date BETWEEN ADDDATE(LAST_DAY(SUBDATE('$date', INTERVAL 1 MONTH)), 1) AND LAST_DAY('$date')
	 AND company_id='$company_id'");
        return $query[0]->amount;
    }

    private function get_current_month_expense() {
        $company_id = company_id();
        $date       = date("Y-m-d");
        $query      = DB::select("SELECT IFNULL(SUM(amount),0) as amount FROM transactions WHERE dr_cr='dr'
	 AND trans_date BETWEEN ADDDATE(LAST_DAY(SUBDATE('$date', INTERVAL 1 MONTH)), 1) AND LAST_DAY('$date')
	 AND company_id='$company_id'");
        return $query[0]->amount;
    }

    public function current_day_income(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function current_day_expense(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function current_month_income(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function current_month_expense(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function yearly_income_vs_expense(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function latest_income(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function latest_expense(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function monthly_income_vs_expense(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function financial_account_balance(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function income_user(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }

    public function income_produk(){
        // Use for Permission Only
        return redirect()->route('dashboard');
    }


}