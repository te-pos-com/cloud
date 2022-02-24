<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Utilities\Overrider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Overrider::load("Settings");
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        $trial_period = get_option('trial_period', 7);

        if ($trial_period < 1) {
            $valid_to = date('Y-m-d', strtotime(date('Y-m-d') . " -1 day"));
        } else {
            $valid_to = date('Y-m-d', strtotime(date('Y-m-d') . " + $trial_period days"));
        }
 
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'user_type'         => 'user',
            'email_verified_at' => get_option('email_verification') == 'disabled' ? now() : null,
            'status'            => 1,
            'valid_to'          => $valid_to,
            'membership_type'   => 'trial',
            'profile_picture'   => 'default.png',
            'email'             => $data['email'],
            'jenis_langganan'   => $data['jenis_langganan'],
            'cabang'            => $data['cabang'],
            'password'          => Hash::make($data['password']),
        ]);

        //Trigger Verified Event
        event(new \Illuminate\Auth\Events\Verified($user));

        return $user;
    }
}
