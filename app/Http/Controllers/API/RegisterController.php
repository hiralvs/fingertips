<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Intervention\Image\Facades\Image;

class RegisterController extends BaseController
{
    
    
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
        $input['unique_id'] =  get_unique_id();
        $input['role'] = $input['role'];
        $input['gender'] = $input['gender'];
        if ($request->hasFile('profilepic')) {

            $image = $request->File('profilepic');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['profile_pic'] = $filename;
        }
       
        
        //print_r($input); die;
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }

//     public function editProfile(Request $request)
//     {
//         $message = trans('api.label_api_invalid_profile');
//         $data = array();
//         $user = Auth::user();

//         $query = User::find($user->id);
//         echo "<pre>"; print_r($query); die;
//         if (!empty($request->all()) && !empty($query)) {
//             if ($request->name) {
//                 $query->name = $request->name;
//             }
//             if ($request->email) {
//                 $email = $request->email;
//                 $user_email_exist = ApiUser::select('id')->where('email', $email)->where('id', '<>', $user->id)->count();
//                 if ($user_email_exist == 0) {
//                     $query->email = $request->email;
//                 } else {
//                     $data = (object) array();
//                     $status = 0;
//                     $message = trans('api.label_api_registration_email_exist');
//                     $response = array('status' => $status, 'message' => $message, 'data' => $data);
//                     return response()->json($response, 200);
//                 }
//             }
//             $query->updated_at = Carbon::now();
//             if ($query->save()) {
//                 $status = 1;
//                 $message = trans('api.label_api_success_profile');
//                 $data['user_id'] = $query->id;
//                 $return_user = $query->toArray();
//                 $data = ApiCommonController::removeNullValue($return_user);
//             } else {
//                 $data = (object) array();
//                 $status = 0;
//                 $message = trans('api.label_api_profile_not_successfully_update');
//                 $data['user_id'] = $query->id;
//             }
//         } else {
//             $data = (object) array();
//             $status = 0;
//             $message = trans('api.label_api_profile_para_missing');
//             $data['user_id'] = $query->id;
//         }
//     }
}

?>