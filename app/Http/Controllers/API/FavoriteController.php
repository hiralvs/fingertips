<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Favorites;
use Illuminate\Support\Facades\Auth;
use DB;

class FavoriteController extends Controller
{
    /* Function used to add event,attraction and malls to favorites */
    public function addToFavorites(Request $request)
    {
        $input = array();
        $input['common_id']= $request->id;
        $input['type']= $request->type;
        $exist = Favorites::where(['common_id'=>$request->id,"type"=>$request->type]);
        if($exist->count()>0)
        {
            $response = ['success' => false,'status'=> 404,'message' => 'Favorites already exist'];    
        }
        else
        {
            Favorites::unguard();
            $favorites = Favorites::create($input);
    
            if($favorites)
            {
                $response = ['success' => true,'status' => 200,'message' => 'Added to favorites successfully.','data'=>$favorites];
            }
            else{
                $response = ['success' => false,'status'=> 404,'message' => 'Something Wrong, Not able to add as favorites'];  
            }
        }
        return response()->json($response);
    }


    /* Function used to get favorite event */
    public function eventFavorites(Request $request)
    {
        $url =env('APP_URL');
        $eventFav = Favorites::select('events.id','unique_id','event_image','event_name','location','event_start_date','event_end_date','start_time','end_time','description',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"))->join('events','events.id','=','favorites.common_id')->where('type','event')->get();
        if($eventFav->count() > 0)
        {
            $response = ['success' => true,'status'=> 200,'message' => 'Data Found successfully.','data'=>$eventFav];  
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);

    }

    /* Function used to get favorite malls */
    public function mallsFavorites(Request $request)
    {
        $url =env('APP_URL');
        $mallFav = Favorites::select('shopsandmalls.id','unique_id','image','name','location','openinghrs','closinghrs','description',DB::raw("CONCAT('','$url/public/upload/malls/',shopsandmalls.image) as image"))->join('shopsandmalls','shopsandmalls.id','=','favorites.common_id')->where('favorites.type','malls')->get();
        if($mallFav->count() > 0)
        {
            $response = ['success' => true,'status'=> 200,'message' => 'Data Found successfully.','data'=>$mallFav];  
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);

    }

    /* Function used to get favorite attraction */
    public function attractionFavorites(Request $request)
    {
        $url =env('APP_URL');
        $attractionFav = Favorites::select('attractions.id','unique_id','attraction_image','attraction_name','location','opening_time','closing_time','description',DB::raw("CONCAT('','$url/public/upload/attractions/',attractions.attraction_image) as attraction_image"))->join('attractions','attractions.id','=','favorites.common_id')->where('favorites.type','attraction')->get();
        if($attractionFav->count() > 0)
        {
            $response = ['success' => true,'status'=> 200,'message' => 'Data Found successfully.','data'=>$attractionFav];  

        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);

    }
}
