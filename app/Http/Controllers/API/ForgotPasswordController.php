<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Password;
use Auth;
use Hash;
use App\User;


class ForgotPasswordController extends BaseController
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
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $user = User::where('email',$request->email);
                if($user.count() > 0)
                {
                    $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                        //$message->from('hiral.devstree@gmail.com');
                        $message->subject('Reset Password');
                    });
                    switch ($response) {
                        case Password::RESET_LINK_SENT:
                            return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                        case Password::INVALID_USER:
                            return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
                    }
                }
                else
                {
                    return \Response::json(array("status" => 400, "message" => 'The email provided is incorrect, please try again.', "data" => array()));
                }
            } catch (\Swift_TransportException $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            } catch (Exception $ex) {
                $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
            }
            return \Response::json($arr);
        }
    }
    
    public function changePassword(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->sendError('400', ['error'=>$validator->errors()->first()]);
            //$arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else { 
            try {
                $user = Auth::guard('api')->user();
                if ((Hash::check(request('old_password'), $user->password)) == false) {
                    return $this->sendError('400', ['error'=>'Check your old password.']);

//                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), $user->password)) == true) {
                    return $this->sendError('400', ['error'=>'Please enter a password which is not similar then current password.']);

                   // $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    $user = User::where('id', $userid)->update(['password' => bcrypt($input['new_password'])]);
                    return $this->sendResponse($user, "Password updated successfully.");

                   // $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                //return $this->sendError('400', ['error'=>$msg]);

                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
       return \Response::json($arr);
    }
}
