<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;


class NotificationController extends BaseController
{
     /*Function Used to get events*/
     public function notification(Request $request)
     {
         $notification = notification::all();
         if($notification->count() > 0)
         {
             $success['data'] = $notification;
             return $this->sendResponse($success, 'Data Found successfully.');
         }
         else
         {   
             return $this->sendError('No Data.', ['error'=>'No Data Found']);
         }
     }
}
