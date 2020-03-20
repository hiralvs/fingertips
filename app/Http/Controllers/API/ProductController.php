<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductController extends Controller
{
    /* Function used to get products */
    public function products(Request $request)
    {
        $url =env('APP_URL');
        $products = Product::select('products.*',DB::raw("CONCAT('','$url/public/upload/products/',product_image) as product_image"))->get();
        if($products->count()>0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$products];
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

}