<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use App\User;
use App\Checkin;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

class CheckinController extends Controller
{
	  /* Function  used to update user profile */ 
    public function checkinUser(Request $request)
    {
        $checkin = Checkin::where('user_id',$request->user_id)->whereNull('checkout_time')->get();
       	if($checkin->count() >0)
       	{          
        	$response = ['success' => true,'data'=> $checkin,'message' => 'You already checkin','status' => 200];  
       	} else 
       	{ 
       		$user = array( 
       			"user_id"=> $request->user_id,
				"property_name"=> $request->property_name, 
				"latitude"=> $request->latitude,
				"longitude"=> $request->longitude, 
				"checkin_time"=> date('Y-m-d h:i:s',strtotime($request->checkin_time))
			);
			Checkin::unguard(); $checkin = Checkin::create($user);
          
        $response = ['success' => true,'data'    => $checkin,'message' => 'Checkin successfully.','status' => 200];  
		}      

        return response()->json($response);
    }
}