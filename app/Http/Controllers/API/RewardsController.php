<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Rewards;
use Illuminate\Support\Facades\Auth;


class RewardsController extends Controller
{
    /*Function Used to get events*/
    public function rewards(Request $request)
    {
        $userid = Auth::guard('api')->user()->id;

        $rewards = Rewards::where('user_id',$userid)->get();
        if($rewards->count() > 0)
        {
            $rewards[0]->used = is_null($rewards[0]->used) ? "" :$rewards[0]->used;
            $rewards[0]->redeem = is_null($rewards[0]->redeem) ? "" :$rewards[0]->redeem;
            unset($rewards[0]->deleted_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$rewards];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }
}
