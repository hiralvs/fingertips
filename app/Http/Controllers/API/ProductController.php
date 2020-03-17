<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductController extends BaseController
{
    /* Function used to get products */
    public function products(Request $request)
    {
        $products = Product :: all();
        if($products->count()>0)
        {
            $success['products'] = $products;
        
            return $this->sendResponse($success, 'Data Found Successfully.');
        }
        else
        {
            return $this->sendError('Error.', ['error'=>'No Data Found']);
        }
    }

    /* Function used to get products details like variant*/
    public function products_variant(Request $request)
    {
        $id = $request->route('id');
        $products = Product::where('id', $id)->get();
        $variant = DB::table('product_variant')->where('product_id',$id)->get();
        if($products->count()>0)
        {
            $success['products'] = $products;
            $success['products']['variant'] = $variant;
        
            return $this->sendResponse($success, 'Product Variant Found Successfully.');
        }
        else
        {
            return $this->sendError('Error.', ['error'=>'No Data Found']);
        }
    }

}
