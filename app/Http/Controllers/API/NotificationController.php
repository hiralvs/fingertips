<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    /*Function Used to get events*/
    public function notification(Request $request)
    {
        $notification = notification::all();
        if($notification->count() > 0)
        {
            unset($notification[0]->deleted_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$notification];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }
}
