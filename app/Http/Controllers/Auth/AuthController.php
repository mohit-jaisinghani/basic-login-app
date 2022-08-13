<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UserService;
use Session;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Cache\RateLimiter;
use Redirect;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service  = $service;
    }

    public function index(Request $request)
    {
        $data = (!empty($request->all())) ? $request->all()['data'] : [
            'loginAttempts' => 0,
            'decaySeconds' => 30,
            'remainingAttempts' => 2
        ];

        return view('auth.login')->with('data', $data);
    }

    public function registration()
    {
        return view('auth.registration');
    }
      
    public function postLogin(Request $request)
    {
        $successfulLogin = false;
        $maxAttempts = 2;
        $decaySeconds = 60;
        $key = $this->throttleKey($request->email);

        $loginAttempts = ($this->limiter()->attempts($key)) ? $this->limiter()->attempts($key) : 0;
        $remainingAttempts = $this->limiter()->retriesLeft($key, $maxAttempts);
        
        $data = [
            'loginAttempts' => $loginAttempts,
            'decaySeconds' => $decaySeconds,
            'remainingAttempts' => $remainingAttempts
        ];

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;

        if (Auth::attempt($credentials)) {
            $successfulLogin = true;
            $message = 'You are successfully logged in.';
        } else {
            if ($loginAttempts == $maxAttempts) {
                // $this->limiter()->availableIn($key)
                $message = 'Maximum incorrect attempts reached. Please try again after 30 seconds.';
            } else if($loginAttempts > $maxAttempts) {
                $this->service->update(['status' => false], $request->email);

                try {
                    $details = [
                        'title' => 'User has been blocked.',
                        'body' => 'User has been blocked.'
                    ];
                   
                    Mail::to($request->email)->send(new \App\Mail\Mailer($details));
                }catch (\Exception $exception) {

                }
                $message = 'User has been blocked.';
            } else {
                $message = 'Oops! You have entered invalid credentials.';
            }

            //Incrementing the attempts
            $this->limiter()->hit(
                $key, $decaySeconds // <= 60 seconds
            );
        }            

        Session::flash('message', $message);
        if ($successfulLogin) {
            Session::flash('alert-class', 'alert-success');
            return redirect()->intended('dashboard');
        } else {
            Session::flash('alert-class', 'alert-danger');
            return redirect()->route('login', ['data' =>  $data]);
        }
    }
      
    public function postRegistration(Request $request)
    {  
        Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|without_spaces',
        ],[
            'password.without_spaces' => 'Password cannot contain spaces.',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        $this->service->store($data);

        Session::flash('message', 'Registered Successfully.');
        Session::flash('alert-class', 'alert-success');
        return redirect("login");
    }
    
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
        
        Session::flash('message', 'Oops! You do not have access');
        Session::flash('alert-class', 'alert-danger');
        return redirect("login");
    }
    
    public function logout() {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }

    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    protected function throttleKey($email)
    {
        return Str::lower($email).'|'.\Request::ip();
    }
}
