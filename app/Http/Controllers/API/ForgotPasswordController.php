<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Password;
use Auth;
use App\User;


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
                $user = User::where('email',$request->email);
                if($user->count() > 0)
                { 
                    $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                        //$message->from('hiral.devstree@gmail.com');
                        $message->subject('Reset Password');
                    });
                    switch ($response) {
                        case Password::RESET_LINK_SENT:
                            return \Response::json(array('success' => true,"status" => 200, "message" => trans($response), "data" => array()));
                        case Password::INVALID_USER:
                            return \Response::json(array('success' => false,"status" => 400, "message" => trans($response), "data" => array()));
                    }
                }
                else
                {
                    return \Response::json(array('success' => false,"status" => 400, "message" => 'The email provided is incorrect, please try again.', "data" => array()));
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
