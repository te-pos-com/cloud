<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UtilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//Default Settings
		DB::table('settings')->insert([
			[
			  'name' => 'mail_type',
			  'value' => 'mail'
			],
			[
			  'name' => 'backend_direction',
			  'value' => 'ltr'
			],
			[
			  'name' => 'membership_system',
			  'value' => 'enabled'
			],
			[
			  'name' => 'trial_period',
			  'value' => '7'
			],
			[
			  'name' => 'monthly_cost',
			  'value' => '10'
			],
		  	[
			  'name' => 'yearly_cost',
			  'value' => '99'
			],
			[
			  'name' => 'allow_singup',
			  'value' => 'yes'
			],			
		]);
		
		//Email Template
		DB::table('email_templates')->insert([
			[
			  'name' => 'registration',
			  'subject' => 'Registration Sucessfully',
			  'body' => '<h3 style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Registration Successful</h3><p style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><br></p><p style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Welcome&nbsp;{name},<br></p><p><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Your account is ready for use. Now you can login to your account using your email and password.<br><br>Thank You<br>Tricky Code<br></span></p>',
			],
			[
			  'name' => 'premium_membership',
			  'subject' => 'Premium Membership',
			  'body' => '<h3 style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Account Update Sucessfully</h3><p style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><br></p><div style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><div><div></div><div></div></div><div><div></div></div></div><p><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Hello {name},</span><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Your account has renewed successfully. Your account is valid until&nbsp;</span><span style="background-color: rgb(245, 245, 245); color: rgb(51, 51, 51); font-family: Menlo, Monaco, Consolas, &quot;Courier New&quot;, monospace; font-size: 13px;">{valid_to}</span><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">.&nbsp;</span></p><p><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Thank You</span><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Tricky Code</span><br></p>',
			],
			[
			  'name' => 'alert_notification',
			  'subject' => 'Smart Cash Membership Extended',
			  'body' => '<h3 style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Account Renew Notification</h3><p style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><br></p><div style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><div><div></div><div></div></div><div><div></div></div></div><p><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Hello {name},</span><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Please renew your account before expired. You account will inactive after {</span><span style="background-color: rgb(245, 245, 245); color: rgb(51, 51, 51); font-family: Menlo, Monaco, Consolas, &quot;Courier New&quot;, monospace; font-size: 13px;">valid_to</span><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">}. So please renew your account before {</span><span style="background-color: rgb(245, 245, 245); color: rgb(51, 51, 51); font-family: Menlo, Monaco, Consolas, &quot;Courier New&quot;, monospace; font-size: 13px;">valid_to</span><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">}. You can contact with your customer support for more information.&nbsp;</span></p><p><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Thank You</span><br style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;"><span style="color: rgb(85, 85, 85); font-family: &quot;PT Sans&quot;, sans-serif;">Tricky Code</span><br></p>',
			],			
		]);
		
    }
}
