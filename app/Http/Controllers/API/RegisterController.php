<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Intervention\Image\Facades\Image;
use App\Otp;
use App\Settings;
use App\Rewards;
use Illuminate\Support\Facades\Config;
use App\Mail\VerificationEmail;
use DB;
use Stripe;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;


class RegisterController extends Controller
{
    
    /* Function used to register user in app with otp*/
    
    public function register(Request $request)
    {   
        if(isset($request->social_type) && $request->social_type != "")
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'social_id' => 'required',
            ]);
        }
        else
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                //'dob' => 'required',
                'role' => 'required',
                //'gender' => 'required',
                'mobile' => 'required',
            ]);
        }
           
        if($validator->fails()){
            $response = [
                'success' => false,
                'status'=> 404,
                'message' => 'Validation Error.',
                'data' => $validator->errors()
            ];
        }
        else
        {
        	try
        	{
                if(isset($request->social_type) && $request->social_type != "")
                {
                    $usercount = User::where('email',$request->email)->get()->count();

                    if($usercount > 0)
                    {
                        $user = User::where('email',$request->email)->first();

                        $user->name = $request->name;
                        $user->email = $request->email;
                        $user->role  = $request->role;
                        $user->social_id = $request->social_id;
                        $user->social_type = $request->social_type;

                        User::unguard();

                        $user->save();
                        $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
                        $user->gender = (is_null($user->gender) || $user->gender == "") ? "":$user->gender;
                        $user->dob = (is_null($user->dob) || $user->dob == "") ? "":$user->dob;
                        $user->customer_id = (is_null($user->customer_id) || $user->customer_id == "") ? "":$user->customer_id;
                        $response = [
                            'success' => true,
                            'status' => 200,
                            'token' => $user->createToken('MyApp')->accessToken,
                            'data'    => $user,
                            'message' => 'User register successfully.',
                        ];

                        return response()->json($response);
                    }
                    else
                    {
                        $input = array();
                        $input = array(
                            'unique_id' => get_unique_id('users'), 
                            'name' => $request->name,
                            'email' => $request->email,
                            'role'=> $request->role,
                            'social_id' => $request->social_id,
                            'social_type' => $request->social_type,
                            'email_verification_token' => Str::random(32)
                        ); 
                        
                        User::unguard();
                        $user = User::create($input);   
                    }                    
                }
                else
                {
                    $request->request->remove('c_password');
    	            $input = $request->all();

    	            $input['password'] = bcrypt($input['password']);
    	            $input['unique_id'] =  get_unique_id('users');
    	            $input['role'] = $input['role'];
    	            $input['gender'] = isset($input['gender']) ? $input['gender'] : NULL;
    	            $input['mobile'] = $input['mobile'];
                    $input['dob'] = isset($input['dob']) ? date('Y-m-d',strtotime($input['dob'])) : NULL;
                    $input['email_verification_token'] = Str::random(32);
    	            if ($request->hasFile('profilepic')) {
    	    
    	                $image = $request->File('profilepic');
    	                $filename = time() . '.' . $image->getClientOriginalExtension();
    	    
    	                $path = public_path('upload/' . $filename);
    	    
    	                Image::make($image->getRealPath())->save($path);
    	                $input['profile_pic'] = $filename;
    	            }
                    
                    User::unguard();
                    $user = User::create($input);
                }
	           // $otp = rand(100000,999999);
               
	            // code to send otp to user
	            if($user->id)
	            {
	                // $data = array();
	    
	                // $otpdata['user_id'] = $user->id;
	                // $otpdata['otp'] = $otp;
	                
	                // $data['TO'] = $user->email;
	                // $data['FROM'] =  Config::get('constants.SMTP_FROM'); 
	                // $data['SITE_NAME'] = Config::get('constants.SITE_NAME');
	                // $data['SUBJECT'] = 'Fingertips-Otp';
	                // $data['VIEW'] = 'mails.otp';
	                // $data['PARAM'] = array('name' => $user->name, 'otp' => $otp);
	                // $data['name'] = $user->name;
                    // $data['otp'] = $otp;
                    $data['TO'] = $user->email;
	                $data['FROM'] =  Config::get('constants.SMTP_FROM'); 
	                $data['SITE_NAME'] = Config::get('constants.SITE_NAME');
	                $data['SUBJECT'] = 'Fingertips-Verification';
	                $data['VIEW'] = 'mails.verifyEmail';
	                $data['PARAM'] = array('name' => $user->name, 'email_verification_token' => $user->email_verification_token);
                    $data['name'] = $user->name;
                    $data['email_verification_token'] = $user->email_verification_token;
                    //$send_mail = \Mail::to($user->email)->send(new VerificationEmail($user));
                    $send_mail = send($data);
                 //    //print_r($send_mail);die;

	                // // if($send_mail)
	                // // {
	                // //     Otp::unguard();
	                // //     $otpinsert = Otp::create($otpdata);
	                // // }
	                
	                /* Add rewards for signup*/
                    $point = Settings::where('type','Signup')->get();
                    $rewards = array();
                    $rewards['user_id'] = $user->id;
                    $rewards['earned'] = $point[0]->value;
                    Rewards::unguard();
                    $setting = Rewards::create($rewards);
	              
	    
	                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
	    
	                try {
	    
	                    $customer = \Stripe\Customer::create([
	                        'email' => $user->email,
	                        'name' => $user->name,
	                        'description' => 'My First Test Customer (created for API docs)',
	                    ]);
	                    if($customer)
	                    {  
	                        $updateuser = User::where('id', $user->id)->update(['customer_id' => $customer->id]);
	                    }
	                } catch (\Exception $ex) {
	                    $response = ['status' => 404,'success' => false,'message' => $ex->getMessage()]; 
	                }
	                $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
                    $user->gender = (is_null($user->gender) || $user->gender == "") ? "":$user->gender;
                    $user->dob = (is_null($user->dob) || $user->dob == "") ? "":$user->dob;
                    $user->customer_id = (is_null($user->customer_id) || $user->customer_id == "") ? "":$user->customer_id;

	                $response = [
	                    'success' => true,
	                    'status' => 200,
	                    'token' => $user->createToken('MyApp')->accessToken,
	                    'data'    => $user,
	                    'message' => 'User register successfully.',
	                ];
	            }
        	}
        	catch (\Exception $ex) {
                //return $ex->getMessage();
	            $response = ['status' => 404,'success' => false,'message' => 'Email Id Already Exist']; 
            }
        }       

        return response()->json($response);
       
    }

    /* Function  used to login */ 
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success' => false,
                'status'=> 404,
                'message' => 'Validation Error.',
                'data' => $validator->errors()
            ];     
        }
        else
        {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                $user = Auth::user();
                unset($user->email_verified_at,$user->deleted_at);
                $user->dob = is_null($user->dob) ? "":$user->dob;
                $user->gender = is_null($user->gender) ? "":$user->gender;
                $user->mobile = is_null($user->mobile) ? "":$user->mobile;
                $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
                $user->customer_id = (is_null($user->customer_id) || $user->customer_id == "") ? "":$user->customer_id;
                $response = [
                    'success' => true,
                    'token' => $user->createToken('MyApp')-> accessToken,
                    'data'    => $user,
                    'message' => 'User login successfully.',
                    'status' => 200,
                ];
            } 
            else{ 
                $response = [
                    'status' => 404,
                    'success' => false,
                    'message' => 'The email or password is incorrect, please try again',
                ];
            } 
        }      

        return response()->json($response);

    }

    /* Function  used to verify otp login */ 
    public function otpverify(Request $request)
    {
        $userverify = Otp::where('user_id',$request->user_id)->where('status','0')->where('otp',$request->otp)->get();
        
        if($userverify->count() > 0){ 
            $response = ['status' => 200,'success' => true,'message' => 'User login successfully.']; 
        } 
        else{
            $response = ['status' => 404,'success' => false,'message' => 'Please try again']; 
        } 
        return response()->json($response);

    }

    /* Function  used to get login details */ 
    public function editProfile()
    {
        $user = auth()->user();
        unset($user->email_verified_at,$user->deleted_at);
        $user->dob = is_null($user->dob) ? "":$user->dob;
        $user->gender = is_null($user->gender) ? "":$user->gender;
        $user->mobile = is_null($user->mobile) ? "":$user->mobile;
        $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
     
        $response = ['status' => 200,'success' => true,'data'=> $user,'message' => 'Data Found Successfully']; 
        return response()->json($response);
    }

    /* Function  used to update user profile */ 
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'dob' => 'required',
            'gender' => 'required'
        ]);
   
        if($validator->fails()){
            $response = ['success' => false,'message' => 'Validation Error.','data' => $validator->errors()];
        }
        else
        {
            $user = User::find(Auth::id());
       
            $user->name = $request->name;
            $user->email = $user->email;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
    
            $user->save();
            unset($user->email_verified_at,$user->deleted_at);
            $user->dob = is_null($user->dob) ? "":$user->dob;
            $user->gender = is_null($user->gender) ? "":$user->gender;
            $user->mobile = is_null($user->mobile) ? "":$user->mobile;
            $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
         
            $response = ['success' => true,'data'    => $user,'message' => 'Data Updated successfully.','status' => 200];
        }       

        return response()->json($response);
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
            $arr = array("status" => 400, "message" => $validator->errors()->first());
        } else { 
            try {
                $user = Auth::guard('api')->user();
                if ((Hash::check(request('old_password'), $user->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.");
                } else if ((Hash::check(request('new_password'), $user->password)) == true) {
                   $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.");
                } else {
                    $user = User::where('id', $userid)->update(['password' => bcrypt($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.",);
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
       return \Response::json($arr);
    }

    /* Functio used to verify email*/

    public function verifyEmail(Request $request,$token = null)
    {
        $token = $request->route('token');

        if($token == null) {
           return redirect()->route('loginpage')->with('error', trans('Invalid Login attempt'));
        }

       $user = User::where('email_verification_token',$token)->first();

       if($user == null ){
           return redirect()->route('loginpage')->with('error', trans('Invalid Login attempt'));
       }
       else
       {
            $user->email_verified = '1';
            $user->email_verified_at = Carbon::now();
            $user->email_verification_token = '';

            $user->save();
            return redirect()->route('loginpage')->with('success', trans('Your account is activated, you can log in now into app'));
       }

    }

    /* Functio used to verify email*/
    public function resendverifyEmail(Request $request)
    {
        $id = $request->route('id');
        $user = User::where('id',$id)->first();
        $user->email_verified = '0';
        $user->email_verification_token = Str::random(32);
        $user->save();

        $data['TO'] = $user->email;
        $data['FROM'] =  Config::get('constants.SMTP_FROM'); 
        $data['SITE_NAME'] = Config::get('constants.SITE_NAME');
        $data['SUBJECT'] = 'Fingertips-Verification';
        $data['VIEW'] = 'mails.verifyEmail';
        $data['PARAM'] = array('name' => $user->name, 'email_verification_token' => $user->email_verification_token);
        $data['name'] = $user->name;
        $data['email_verification_token'] = $user->email_verification_token;
        //$send_mail = \Mail::to($user->email)->send(new VerificationEmail($user));
        $send_mail = send($data);

        if($send_mail)
        {
            unset($user->email_verification_token,$user->email_verified_at,
                $user->dob,$user->gender,$user->customer_id,$user->deleted_at);
            $user->profile_pic = (is_null($user->profile_pic) || $user->profile_pic == "") ? "":env('APP_URL').'public/upload/'.$user->profile_pic;
            $response = [
                        'success' => true,
                        'status' => 200,
                        'token' => $user->createToken('MyApp')->accessToken,
                        'data'    => $user,
                        'message' => 'Email verification link send successfully.',
                    ];
        }
        else
        {
            $response = ['status' => 404,'success' => false,'message' => 'Email Id Already Exist']; 
        }

        return response()->json($response);
    }
}
?>