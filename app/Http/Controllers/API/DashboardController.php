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
        $trendingnow = Trendingnow::select('trending_now.title','trending_now.id','trending_now.link','trending_now.status',DB::raw("CONCAT('','$url/public/upload/trending/',image) as trendingimage"))->get();
        $sponsor = Sponsor::select('sponsors.*',DB::raw("CONCAT('','$url/public/upload/sponsors/',image) as trendingimage"))->get();
        $events = Events::select('events.id','events.event_name','events.event_start_date','events.event_end_date','events.start_time','events.end_time','events.category_id',DB::raw("CONCAT('','$url/public/upload/events/',event_image) as event_image"))->limit(10)->get();
        $malls = ShopsandMalls::select('shopsandmalls.id','shopsandmalls.name','shopsandmalls.location','shopsandmalls.openinghrs','shopsandmalls.closinghrs','shopsandmalls.contact','shopsandmalls.type',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->limit(10)->get();
        $attraction = Attractions::select('attractions.id','attractions.attraction_name','attractions.location','attractions.cost','attractions.opening_time','attractions.closing_time','attractions.contact',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->limit(10)->get();
        if($trendingnow->count() > 0 || $sponsor->count() > 0 || $events->count() > 0 || $malls->count() > 0 || $attraction->count() > 0)
        {
            unset($trendingnow[0]->deleted_at,$trendingnow[0]->updated_at);
            if($sponsor->count() > 0)
                unset($sponsor[0]->deleted_at,$sponsor[0]->updated_at);
            if($events->count() > 0)
                unset($events->deleted_at,$events[0]->updated_at);
            if($malls->count() > 0)
                unset($malls[0]->deleted_at,$malls[0]->updated_at);
            if($attraction->count() > 0)
                unset($attraction[0]->deleted_at,$attraction[0]->updated_at);
            
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
