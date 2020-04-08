<?php

namespace App\Http\Controllers\Auth;
use Session,Redirect,Auth,DB;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        ini_set('display_errors',1);
    }

    public function index()
    {
        $users = Auth::user();
        if(!empty($users)){
            return redirect()->route('dashboard');
        }
       return view('auth.login');
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

    public function postLogin(Request $request){
        $email = $request->email;
        $password = $request->password;
        if($email && $password){
            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                $users = Auth::user(); 
                if(empty($users->deleted_at)){
                    if($users->status == "0"){ 
                        // Session::put('lang_id', '1');
                        // Session::put('lang_name', 'English');
                        // Session::put('lang_code', 'en');
                        return redirect()->route('dashboard')->with('success', trans('Login Successfully'). $users->name);
                    } else {
                        Auth::logout();
                        return redirect()->route('loginpage')->with('error', trans('User seems to be inactive.'));
                    }
                } else {
                    Auth::logout();
                    return redirect()->route('loginpage')->with('error', trans('User seems to be deleted'));
                }
            }  else {
                return redirect()->route('loginpage')->with('error', trans('auth.failed'));
            }
        } else {
            return redirect()->route('loginpage')->with('error', trans('Something went wrong.'));
        }
    }

    public function doLogin(Request $request)
    {
       $rules = array(
        'email'    => 'required|email', // make sure the email is an actual email
        'password' => 'required|min:3'
        //'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
    );
    
    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);
    // if the validator fails, redirect back to the form
    if ($validator->fails()) { 
        return Redirect::to('login')
            ->withErrors($validator) // send back all errors to the login form
            ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
    } else {
    
        // create our user data for the authentication
        $userdata = array(
            'email'     => Input::get('email'),
            'password'  => Input::get('password')
        );
    
        // attempt to do the login
        if (Auth::attempt($userdata)) {
    
            // validation successful!
            // redirect them to the secure section or whatever
            // return Redirect::to('secure');
            // for now we'll just echo success (even though echoing in a controller is bad)
            echo 'SUCCESS!';
    
        } else {        
    
            // validation not successful, send back to form 
            return Redirect::to('login');
    
        }
    
    }
    }
}
