<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Order;

class OrderController extends Controller
{
    /*Function Used to get events*/
    public function purchases(Request $request)
    {
        $purchase = Order::select('orders.*','orders.id as oid','order_details.*','products.name','products.product_image','products.price')->join('order_details','order_details.order_id','=','orders.order_id')->leftjoin('products', 'products.id', '=', 'orders.product_id')->get();
        if($purchase->count() > 0)
        {
            unset($purchase[0]->deleted_at);
            $purchase[0]->size = is_null($purchase[0]->size) ? "" : $purchase[0]->size;
            $purchase[0]->discount = is_null($purchase[0]->discount) ? "" : $purchase[0]->discount;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$purchase];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }
}
