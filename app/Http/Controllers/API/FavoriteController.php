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


class FavoriteController extends Controller
{
    /* Function used to add event,attraction and malls to favorites */
    public function addToFavorites(Request $request)
    {
        $input = $request->all();
        $input['common_id']= $request->id;
        $input['type']= $request->type;
        $favorites = Favorites::create($otpdata);

        if($favorites)
        {
            return $this->sendResponse($success, 'Added to favorites successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'The email or password is incorrect, please try again']);
        }
    }
}
