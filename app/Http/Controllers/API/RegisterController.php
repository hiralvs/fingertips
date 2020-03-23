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
use DB;
use Stripe;
use Hash;


class RegisterController extends Controller
{
    
    /* Function used to register user in app with otp*/
    
    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'dob' => 'required',
            'role' => 'required',
            'gender' => 'required',
            'mobile' => 'required'
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
        	try
        	{
	            $input = $request->all();
	            $input['password'] = bcrypt($input['password']);
	            $input['unique_id'] =  get_unique_id('users');
	            $input['role'] = $input['role'];
	            $input['gender'] = $input['gender'];
	            $input['mobile'] = $input['mobile'];
	            $input['dob'] = date('Y-m-d',strtotime($input['dob']));
	            if ($request->hasFile('profilepic')) {
	    
	                $image = $request->File('profilepic');
	                $filename = time() . '.' . $image->getClientOriginalExtension();
	    
	                $path = public_path('upload/' . $filename);
	    
	                Image::make($image->getRealPath())->resize(50, 50)->save($path);
	                $input['profile_pic'] = $filename;
	            }
	            $otp = rand(100000,999999);
	            
	            $user = User::create($input);
	                echo "<pre>";
	            print_r($user);
	            // code to send otp to user
	            if($user->id)
	            {
	                $data = array();
	    
	                $otpdata['user_id'] = $user->id;
	                $otpdata['otp'] = $otp;
	                
	                $data['TO'] = $user->email;
	                $data['FROM'] =  Config::get('constants.SMTP_FROM'); 
	                $data['SITE_NAME'] = Config::get('constants.SITE_NAME');
	                $data['SUBJECT'] = 'Fingertips-Otp';
	                $data['VIEW'] = 'mails.otp';
	                $data['PARAM'] = array('name' => $user->name, 'otp' => $otp);
	                $data['name'] = $user->name;
	                $data['otp'] = $otp;
	                $send_mail = send($data);
	                if($send_mail)
	                {
	                    Otp::unguard();
	                    $otpinsert = Otp::create($otpdata);
	                }
	                
	                if($otpinsert)
	                {
	                    $point = Settings::where('type','Signup')->get();
	                    $rewards = array();
	                    $rewards['user_id'] = $user->id;
	                    $rewards['earned'] = $point[0]->value;
	                    $setting = Rewards::create($rewards);
	                }
	    
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

}

?>