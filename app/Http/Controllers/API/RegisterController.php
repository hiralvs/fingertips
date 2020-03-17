<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
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

class RegisterController extends BaseController
{
    
    /* Function used to register user in app with otp*/
    
    public function register(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
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

            $create = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'description' => 'My First Test Customer (created for API docs)',
            ]);

            if($create)
            {
                $success['create'] = $create;
            }
            else{
                return $this->sendError('Unauthorised.', ['error'=>$create]);

            }
                            
        }
        ////////////////////////

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /* Function  used to login */ 
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'The email or password is incorrect, please try again']);
        } 
    }

    /* Function  used to login */ 
    public function otpverify(Request $request)
    {
        $userverify = Otp::where('user_id',$request->user_id)->where('status','0')->where('otp',$request->otp)->get();
        
        if($userverify->count() > 0){ 

            return $this->sendResponse('', 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }


    /* Function  used to get login details */ 

    public function editProfile()
    {
        $success['user'] = auth()->user();
        return $this->sendResponse($success, 'Data Found Successfully');
    }

    /* Function  used to update user profile */ 
    public function updateProfile(Request $request)
    {
        // $this->validate($request, [
        //     'current' => 'required',
        //     'password' => 'required|confirmed',
        //     'password_confirmation' => 'required'
        // ]);
        $user = User::find(Auth::id());

        $user->name = $request->name;
        $user->email = $user->email;
        $user->dob = $request->dob;
        $user->gender = $request->gender;

        $user->save();

        $success['user'] = $user;
        return $this->sendResponse($success, 'Data Updated Successfully');

    }

}

?>