<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Cart;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductController extends Controller
{
    /* Function used to get products */
    public function products(Request $request)
    {
        $url =env('APP_URL');
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit?  $request->limit : 10;
        $offset = ($page - 1) * $limit;
        $products = Product::select('products.*',DB::raw("CONCAT('','$url/public/upload/products/',product_image) as product_image"))->offset($offset)->limit($limit)->get();
        $totalrecords = Product::all()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        if($products->count()>0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','total'=> $totalrecords,"total_page"=> $totalpage,"page"=> $page,"limit"=> $offset,'data'=>$products];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    /* Function used to get products details like variant*/
    public function products_variant(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id');
        $products = Product::select('products.*',DB::raw("CONCAT('','$url/public/upload/products/',product_image) as product_image"))->where('id', $id)->get();
        $variant = DB::table('product_variant')->where('product_id',$id)->get();
        if($products->count()>0)
        {
            unset($products[0]->updated_at,$products[0]->deleted_at);
            $success['products'] = $products;
            $success['variant'] = $variant;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);

    }

    public function addToCart(Request $request)
    {
        $product_id = $request->product_id;
        $user_id = $request->user_id;
        $quantity = $request->quantity;

        $cartData = Cart::where(['product_id'=>$product_id,'user_id'=>$user_id])->get();
        if($cartData->count() > 0)
        {
            $response = ['success' => false,'status'=> 404,'message' => 'Item already Exist in your cart'];
        }
        else
        {
            $cart = array(
                'user_id'=>$user_id,
                'product_id'=>$product_id,
                'quantity'=>$quantity,
            );
            Cart::unguard();
            $success = Cart::create($cart);
            if($success)
            {
                $response = ['success' => true,'status' => 200,'message' => 'Item Added to Cart  Successfully.','data'=>$success];
            }
        }
        return response()->json($response);
    }

    public function updateCart(Request $request)
    {
        $cart_data = $request->getContent();
        $data = json_decode($cart_data,true);
       
        $user_id = $data['user_id'];
        foreach ($data['product'] as $key => $value) {
            $cartData = Cart::where(['product_id'=>$value['product_id'],'user_id'=>$user_id])->first();
            if($cartData)
            {
                $cartData->quantity = $value['quantity'];
                Cart::unguard();
                $success = $cartData->save();

            }
        }

        if($success)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Cart Updated Successfully.','data'=>$success];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'Cart Not Updated'];
        }

        return response()->json($response);
    }

    public function deleteCart(Request $request)
    {
        $user_id = $request->user_id;
        $product_id = $request->product_id;

        $affectedRows = Cart::where(['product_id'=>$product_id,'user_id'=>$user_id])->delete();

        if($affectedRows)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Item deleted from cart Successfully.','data'=>[]];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'Item not deleted'];
        }
        return response()->json($response);
    }

}
