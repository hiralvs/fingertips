<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\ShopsandMalls;
use App\Attractions;
use App\Events;
use App\Favorites;
use Illuminate\Support\Facades\Auth;


class FavoriteController extends BaseController
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
            return $this->sendError('Error.', ['error'=>'Favorites already exist']);

        }
        else
        {
            Favorites::unguard();

            $favorites = Favorites::create($input);
            $success['data']=$favorites;
    
            if($favorites)
            {
                return $this->sendResponse($success, 'Added to favorites successfully.');
            }
            else{
                return $this->sendError('Error.', ['error'=>'Something Wrong, Not able to add as favorites']);
            }
        }
       
    }


    /* Function used to get favorite event */
    public function eventFavorites(Request $request)
    {
        $eventFav = Favorites::select('events.id','unique_id','event_image','event_name','location','event_start_date','event_end_date','start_time','end_time','description')->join('events','events.id','=','favorites.common_id')->where('type','event')->get();
        if($eventFav->count() > 0)
        {
            $success['data'] = $eventFav;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /* Function used to get favorite malls */
    public function mallsFavorites(Request $request)
    {
        $eventFav = Favorites::select('shopsandmalls.id','unique_id','image','name','location','openinghrs','closinghrs','description')->join('shopsandmalls','shopsandmalls.id','=','favorites.common_id')->where('favorites.type','malls')->get();
        if($eventFav->count() > 0)
        {
            $success['data'] = $eventFav;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

      /* Function used to get favorite attraction */
      public function attractionFavorites(Request $request)
      {
          $eventFav = Favorites::select('attractions.id','unique_id','attraction_image','attraction_name','location','opening_time','closing_time','description')->join('attractions','attractions.id','=','favorites.common_id')->where('favorites.type','attraction')->get();
          if($eventFav->count() > 0)
          {
              $success['data'] = $eventFav;
              return $this->sendResponse($success, 'Data Found successfully.');
          }
          else
          {   
              return $this->sendError('No Data.', ['error'=>'No Data Found']);
          }
      }
}
