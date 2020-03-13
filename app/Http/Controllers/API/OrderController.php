<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Order;

class OrderController extends BaseController
{
    /*Function Used to get events*/
    public function purchases(Request $request)
    {
        $purchase = Order::select('orders.*','orders.id as oid','order_details.*','products.name','products.product_image','products.price')->join('order_details','order_details.order_id','=','orders.order_id')->leftjoin('products', 'products.id', '=', 'orders.product_id')->get();
        if($purchase->count() > 0)
        {
            $success['data'] = $purchase;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }
}
