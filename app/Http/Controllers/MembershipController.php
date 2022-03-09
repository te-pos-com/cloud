<?php

namespace App\Http\Controllers;

use App\EmailTemplate;
use App\Mail\PremiumMembershipMail;
use App\Mail\PaymentConfirmationMail;
use App\PaymentHistory;
use App\Utilities\Overrider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Stripe;
use Validator;
use DB;

class MembershipController extends Controller {
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function extend() {
        return view('membership.extend');
    }

    public function pay(Request $request) {
        $validator = Validator::make($request->all(), [
            'extend_type' => 'required',
            'year'        => 'required_without:month',
            'month'       => 'required_without:year',
            'gateway'     => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('membership/extend')
                ->withErrors($validator)
                ->withInput();
        }

        $data = array();
        if ($request->extend_type == "yearly") {
            $data['title']  = "Extend Membership for " . $request->year . " year and " .cabang_aktif() . " Cabang";
            $data['amount'] = get_option('yearly_cost')*cabang_aktif() * $request->year;
            $data['custom'] = $request->year . ' year';
        } else if ($request->extend_type == "montly") {
            $data['title']  = "Extend Membership for " . $request->month . " month  and " .cabang_aktif() . " Cabang";
            $data['amount'] = get_option('monthly_cost')*cabang_aktif() * $request->month;
            $data['custom'] = $request->month . ' month';
        }

        //Remove Unused History
        $payment = PaymentHistory::where('status', 'pending')->where('user_id', company_id())->delete();

        //Create Pending Payment
        $payment              = new PaymentHistory();
        $payment->user_id     = company_id();
        $payment->title       = $data['title'];
        $payment->method      = $request->gateway;
        $payment->amount      = $data['amount'];
        $payment->extend_type = $request->extend_type;
        $payment->extend      = $data['custom'];
        $payment->status      = 'pending';
        $payment->save();

        $data['payment_id'] = $payment->id;
        $data['gateway']    = $request->gateway;
        $data['payment']    = $payment;
        
        
        $user             = \App\User::find(company_id());
        //Replace paremeter
        $replace = array(
            '{name}'    => $user->name,
            '{no}'      => $payment->id,
            '{nominal}' => number_format($data['amount']),
        );
        
        
        //Send email Confrimation
        Overrider::load("Settings");
        $template       = EmailTemplate::where('name', 'payment_confirmation')->first();
        $template->body = process_string($replace, $template->body);
        try {
			Mail::to($user->email)->send(new PaymentConfirmationMail($template));
		} catch (\Exception $e) {}
        
        return view('membership.extend', $data);

    }

    public function paypal_payment_authorize($paypalOrderId, $payment_id) {
        $payment = PaymentHistory::find($payment_id);

        // Creating an environment
        $clientId     = get_option('paypal_client_id');
        $clientSecret = get_option('paypal_secret');

        if (get_option('paypal_mode') == 'sandbox') {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }

        $client = new PayPalHttpClient($environment);

        $request = new OrdersCaptureRequest($paypalOrderId);
        $request->prefer('return=representation');

        try {
            $response = $client->execute($request);

            if ($response->result->status == 'COMPLETED') {

                DB::beginTransaction();

                $amount    = $response->result->purchase_units[0]->amount->value;
                $increment = $payment->extend;

                if ($amount >= $payment->amount) {
                    $user             = \App\User::find($payment->user_id);
                    $user->valid_to   = date('Y-m-d', strtotime($user->valid_to . " +$increment"));
                    $user->last_email = NULL;
                    $user->membership_type = 'member';
                    $user->save();

                    //Save payment History
                    $payment->method = "PayPal";
                    $payment->status = 'paid';
                    $payment->save();

                    //Replace paremeter
                    $replace = array(
                        '{name}'     => $user->name,
                        '{email}'    => $user->email,
                        '{valid_to}' => date(get_date_format(), strtotime($user->valid_to)),
                    );

                    //Send email Confrimation
                    Overrider::load("Settings");
                    $template       = EmailTemplate::where('name', 'premium_membership')->first();
                    $template->body = process_string($replace, $template->body);
                    try {
						Mail::to($user->email)->send(new PremiumMembershipMail($template));
					} catch (\Exception $e) {}

                }

                DB::commit();

                return redirect()->route('dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
            }
        } catch (HttpException $ex) {
            return back()->with('error', _lang('Sorry, Payment not completed !'));
        }
    }

    //Stripe Payment
    public function stripe_payment_authorize(Request $request, $payment_id) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $payment   = PaymentHistory::find($payment_id);
        $increment = $payment->extend;

        Stripe\Stripe::setApiKey(get_option('stripe_secret_key'));
        $charge = Stripe\Charge::create([
            "amount"      => $payment->amount * 100,
            "currency"    => get_option('currency'),
            "source"      => $request->stripeToken,
            "description" => _lang('Extend Membership'),
        ]);


        $user             = \App\User::find($payment->user_id);
        $user->valid_to   = date('Y-m-d', strtotime($user->valid_to . " +$increment"));
        $user->last_email = NULL;
		$user->membership_type = 'member';
        $user->save();

        //Save payment History
        $payment->method = "Stripe";
        $payment->status = 'paid';
        $payment->save();

        //Replace paremeter
        $replace = array(
            '{name}'     => $user->name,
            '{email}'    => $user->email,
            '{valid_to}' => date(get_date_format(), strtotime($user->valid_to)),
        );

        //Send email Confrimation
        Overrider::load("Settings");
        $template       = EmailTemplate::where('name', 'premium_membership')->first();
        $template->body = process_string($replace, $template->body);
		
		try {
            Mail::to($user->email)->send(new PremiumMembershipMail($template));
        } catch (\Exception $e) {}
        

        return redirect()->route('dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
    }

    public function transfer_payment_authorize($payment_id) {
        $payment = PaymentHistory::find($payment_id);

        
        try {

                DB::beginTransaction();
                $increment = $payment->extend;
                $user             = \App\User::find($payment->user_id);
                $user->valid_to   = date('Y-m-d', strtotime($user->valid_to . " +$increment"));
                $user->last_email = NULL;
                $user->membership_type = 'member';
                $user->save();

                //Save payment History
                $payment->method = "Transfer";
                $payment->status = 'paid';
                $payment->save();

                //Replace paremeter
                $replace = array(
                    '{name}'     => $user->name,
                    '{email}'    => $user->email,
                    '{valid_to}' => date(get_date_format(), strtotime($user->valid_to)),
                );
                
                //Send email Confrimation
                Overrider::load("Settings");
                $template       = EmailTemplate::where('name', 'premium_membership')->first();
                $template->body = process_string($replace, $template->body);
                try {
					Mail::to($user->email)->send(new PremiumMembershipMail($template));
				} catch (\Exception $e) {}

                DB::commit();
                return redirect()->route('dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
        } catch (HttpException $ex) {
            return back()->with('error', _lang('Sorry, Payment not completed !'));
        }
    }


}