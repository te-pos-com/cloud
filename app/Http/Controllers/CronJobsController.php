<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\RepeatTransaction;
use App\User;
use App\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotificationMail;
use App\Utilities\Overrider;
use DB;

class CronJobsController extends Controller
{
	
    /**
     * Show the application CronJobs.
     *
     * @return \Illuminate\Http\Response
     */
    public function run()
    {
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		//Process Repeat Transactions
		$date = date("Y-m-d");
		$repeat_transaction = RepeatTransaction::where('trans_date',$date)
		                                       ->where('status',0)->get();
											   
		foreach($repeat_transaction as $transaction){
			if($transaction->type == 'income'){
				$trans = new Transaction();
				$trans->trans_date = $transaction->trans_date;
				$trans->account_id = $transaction->account_id;
				$trans->chart_id = $transaction->chart_id;
				$trans->type = 'income';
				$trans->dr_cr = 'cr';
				$trans->amount = $transaction->amount;
				$trans->payer_payee_id = $transaction->payer_payee_id;
				$trans->payment_method_id = $transaction->payment_method_id;
				$trans->reference = $transaction->reference;
				$trans->note = $transaction->note;
				$trans->company_id = $transaction->company_id;
				$trans->save();
				
				$transaction->trans_id = $trans->id;
				$transaction->status = 1;
				$transaction->save();		
			}else if($transaction->type == 'expense'){
				$trans= new Transaction();
				$trans->trans_date = $transaction->trans_date;
				$trans->account_id = $transaction->account_id;
				$trans->chart_id = $transaction->chart_id;
				$trans->type = 'expense';
				$trans->dr_cr = 'dr';
				$trans->amount = $transaction->amount;
				$trans->payer_payee_id = $transaction->payer_payee_id;
				$trans->payment_method_id = $transaction->payment_method_id;
				$trans->reference = $transaction->reference;
				$trans->note = $transaction->note;
				$trans->company_id = $transaction->company_id;
				$trans->save();
				
				$transaction->trans_id = $trans->id;
				$transaction->status = 1;
				$transaction->save();
			}
		}
		
		//Send Alert Notification
		$days_before = 7;
		$user_list = DB::select("SELECT users.* FROM users WHERE DATEDIFF(valid_to,CURDATE()) <= $days_before AND last_email IS NULL AND user_type='user'");
        
		if (count($user_list) > 0) {
            foreach ($user_list as $user) {
				//Replace paremeter
				$replace = array(
					'{name}'=>$user->name,
					'{email}'=>$user->email,
					'{valid_to}' =>$user->valid_to,
				);
				
				//Send email Confrimation
				Overrider::load("Settings");
				$template = EmailTemplate::where('name','alert_notification')->first();
				$template->body = process_string($replace,$template->body);	
				try{
					Mail::to($user->email)->send(new AlertNotificationMail($template));
				}catch (\Exception $e) {
					//Noting
				}	
                $u = User::find($user->id);
                $u->last_email = $date;
				$u->save();
            }

        }
		
		echo 'Scheduled task runs successfully';
	
    }

}
