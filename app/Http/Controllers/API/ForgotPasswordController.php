<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Password;
use Auth;
use App\User;
use Illuminate\Support\Facades\Config;


class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
	 * Create a new controller instance.
	 */
	public function __construct()
	{
		$this->middleware('guest');
    }
    
    public function forgotPassword(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
   
        if($validator->fails()){
            $arr = array('success' => false,"status" => 400, "message" => $validator->errors()->first(), "data" => array());
        }
        else {
            try {
                $user = User::where('email',$request->email)->first();
                if(!empty($user))
                { 
                    $otp = rand(100000,999999);
                    $user->otp = $otp;
                    User::unguard();

                    $user->save(); 
                    $data['TO'] = $user->email;
                    $data['FROM'] =  Config::get('constants.SMTP_FROM'); 
                    $data['SITE_NAME'] = Config::get('constants.SITE_NAME');
                    $data['SUBJECT'] = 'Fingertips-Your verification code';
                    $data['VIEW'] = 'mails.forgototp';
                    $data['PARAM'] = array('name' => $user->name, 'otp' => $otp);
                    $data['name'] = $user->name;
                    $data['otp'] = $otp;
                    $send_mail = send($data);

                    if($send_mail)
                    {
                        return \Response::json(array('success' => true,"status" => 200, "message" => trans('Otp Send Successfully for forgot password'), "data" => array()));
                    }
                    else
                    {
                        return \Response::json(array('success' => true,"status" => 200, "message" => trans('Otp not send'), "data" => array()));
                    }
                }
                else
                {
                    return \Response::json(array('success' => false,"status" => 400, "message" => 'Email id is not registered with us.', "data" => array()));
                }
            } catch (\Swift_TransportException $ex) {
                $arr = array('success' => false,"status" => 400, "message" => $ex->getMessage(), "data" => []);
            } catch (Exception $ex) {
                $arr = array('success' => false,"status" => 400, "message" => $ex->getMessage(), "data" => []);
            }
        }
        return \Response::json($arr);

    }
    
   
}
