<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;
use App\Rewards;
use Illuminate\Support\Facades\Auth;


class RewardsController extends BaseController
{
    /*Function Used to get events*/
    public function rewards(Request $request)
    {
        $userid = Auth::guard('api')->user()->id;

        $rewards = Rewards::where('user_id',$userid)->get();
        if($rewards->count() > 0)
        {
            $success['data'] = $rewards;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }
}
