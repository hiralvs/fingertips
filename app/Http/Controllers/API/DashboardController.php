<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\User;
use App\ShopsandMalls;
use App\Attractions;
use App\Events;
use App\Trendingnow;
use App\Sponsor;
use Illuminate\Support\Facades\Auth;
use DB;

class DashboardController extends Controller
{
    /*Function Used to get malldetails page*/
    public function dashboard(Request $request)
    {
        $url = env('APP_URL');
        $trendingnow = Trendingnow::select('trending_now.*',DB::raw("CONCAT('','$url/public/upload/trending_now/',image) as trendingimage"))->get();
        $sponsor = Sponsor::select('sponsors.*',DB::raw("CONCAT('','$url/public/upload/sponsors/',image) as trendingimage"))->get();
        $events = Events::select('events.*',DB::raw("CONCAT('','$url/public/upload/events/',event_image) as event_image"))->get();
        $malls = ShopsandMalls::select('shopsandmalls.*',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->get();
        $attraction = Attractions::select('attractions.*',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->get();
        if($trendingnow->count() > 0 || $sponsor->count() > 0 || $events->count() > 0 || $malls->count() > 0 || $attraction->count() > 0)
        {
            // unset($malls[0]->deleted_at,$malls[0]->updated_at);
            // if($floormap->count() > 0)
            //     unset($floormap[0]->deleted_at,$floormap[0]->updated_at);
            
            $success['trendingnow'] = $trendingnow;
            $success['sponsor'] =$sponsor;
            $success['events'] =$events;
            $success['malls'] = $malls;
            $success['attraction'] =  $attraction ;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];

            //return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }
}
