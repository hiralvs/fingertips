<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\ShopsandMalls;
use App\Attractions;
use App\Events;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomePageController extends BaseController
{
    
    /*Function Used to get events*/
    public function eventListing(Request $request)
    {
        $events = Events::select('id','unique_id','event_image','event_name','start_time','end_time')->get();
        if($events->count() > 0)
        {
            $success['data'] = $events;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }


    /*Function Used to get malls*/
    public function mallListing(Request $request)
    {
        $malls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs')->get();
        if($malls->count() > 0)
        {
            $success['data'] = $malls;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

   /*Function Used to get attraction*/
    public function attractionListing(Request $request)
    {
        $attraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time')->get();
        if($attraction->count() > 0)
        {
            $success['data'] = $events;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }
}
