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
	  /* Function  used to checkin user profile */ 
    public function checkinUser(Request $request)
    {
        $checkin = Checkin::where('user_id',$request->user_id)->whereNull('checkout_time')->get();
       	if($checkin->count() >0)
       	{          
        	$response = ['success' => true,'message' => 'You already checkin','status' => 200];  
       	} else 
       	{ 
       		$user = array( 
       			"user_id"=> $request->user_id,
    				//"property_name"=> $request->property_name, 
    				"latitude"=> $request->latitude,
    				"longitude"=> $request->longitude, 
    				"checkin_time"=> date('Y-m-d h:i:s')
			);
			Checkin::unguard(); 
      $checkin = Checkin::create($user);
          
      $response = ['success' => true,'message' => 'Checkin successfully.','status' => 200];  
		}      

        return response()->json($response);
    }

    /* Function  used to checkout user profile */ 
    public function checkoutUser(Request $request)
    {
        $checkin = Checkin::where('user_id',$request->user_id)->get();
        if($checkin->count() >0)
        { 
          $affected = DB::table('checkin')
              ->where('user_id', $request->user_id)
              ->update(['checkout_time' =>date('Y-m-d h:i:s')]);
          if($affected)
          {
            $response = ['success' => true,'message' => 'You checkout successfully','status' => 200];  
          }
          
        } 
        else 
        { 
          $response = ['success' => false,'message' => 'Record not exist','status' => 404];  
        }      

        return response()->json($response);
    }
}