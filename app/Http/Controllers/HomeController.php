<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checkin;
use App\Attractions;
use App\Event;
use App\ShopsandMalls;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        ini_set('display_errors',1);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function checkinNotification()
    {        
        $url =env('APP_URL');
        $checkin = Checkin::whereNull('checkout_time')->get();
        if($checkin->count()>0)
        {
            foreach ($checkin as $key => $value) {
               $lat = $value->latitude;
               $long = $value->longitude;
               if($value->esma_type == 'event')
               {
                    $query = Event::select('events.id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude','event_start_date',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $long . '))
                     + SIN(RADIANS(' . $lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'));
               }
               else if($value->esma_type == 'attraction')
               {
                    $query = Attractions::select('attractions.id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $long . '))
                     + SIN(RADIANS(' . $lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'));
               }
               else if($value->esma_type == 'malls')
               {
                    $query = ShopsandMalls::select('shopsandmalls.id','unique_id','image','name','openinghrs','closinghrs','shopsandmalls.type','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $long . '))
                     + SIN(RADIANS(' . $lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'));
               }
               echo now();
               $query = $query->get();
               if(strtotime($new_time) > strtotime(now()))
               {
                echo "send push notification";
               }
               

            }
        }
        // else
        // {
        //     $response = ['success' => true,'message' => 'You already checkin','status' => 200];  
        // }
        // return response()->json($response);
    }
}
