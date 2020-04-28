<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Product;
use App\ProductVariant;
use App\FlashSale;
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
        $products = Product::select('products.id','products.unique_id','products.name','category_id','price','stock','status','return_policy','description',DB::raw("CONCAT('','$url/public/upload/products/',product_image) as product_image"))->offset($offset)->limit($limit)->get();
        foreach ($products as $key => $value) {
            $flash_sales = FlashSale::select('flash_sales.name as salename','discount_percentage','discounted_price','end_date','end_time')->where('product_id',$value->id)->where('start_date','>',date('Y-m-d',strtotime(now())))->get();
            if( $flash_sales->count()>0)
            {
                $products[$key]['flash_sales'] = 1;
                $products[$key]['flashname'] = $flash_sales[0]->salename;
                $products[$key]['discount_percentage'] = $flash_sales[0]->discount_percentage;
                $products[$key]['discounted_price'] = $flash_sales[0]->discounted_price;
                $products[$key]['end_date'] = $flash_sales[0]->end_date;
                $products[$key]['end_time'] = $flash_sales[0]->end_time;
            }
            else
            {
                $products[$key]['flash_sales'] = 0;
            }
        }
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
        $variant = ProductVariant::select('product_variant.*',DB::raw("CONCAT('','$url/public/upload/products/',variant_image) as variant_image"),'colors.color as colorname','size.size as sizename')->leftjoin('colors','colors.id','=','product_variant.color')->leftjoin('size','size.id','=','product_variant.size')->where('product_id',$id)->get();
        $flash_sales = FlashSale::select('flash_sales.name as salename','discount_percentage','discounted_price','end_date','end_time')->where('product_id',$id)->where('start_date','>',date('Y-m-d',strtotime(now())))->get();
        $category_id = explode(",", $products[0]->category_id);
        $relatedProducts = Product::select('products.*',DB::raw("CONCAT('','$url/public/upload/products/',product_image) as product_image"))->where('id','!=', $id)->Where(function ($query) use($category_id) {
             for ($i = 0; $i < count($category_id ); $i++){
                $query->orwhere('category_id', 'like',  '%' . $category_id[$i] .'%');
             }      
        })->get();
         //echo "<pre>";print_r($variant);
        if($flash_sales->count() > 0)
        {
            $products[0]['flash_sales'] = 1;
            $products[0]['flashname'] = $flash_sales[0]->salename;
            $products[0]['discount_percentage'] = $flash_sales[0]->discount_percentage;
            $products[0]['discounted_price'] = $flash_sales[0]->discounted_price;
            $products[0]['end_date'] = $flash_sales[0]->end_date;
            $products[0]['end_time'] = $flash_sales[0]->end_time;
        }
        else
        {
            $products[0]['flash_sales'] = 0;
        }
        $tmp = array();
        foreach ( $variant as $key => $value) 
        {
            if(in_array($value->color,$tmp))
            {
                $tmp[$value->color]['id'] = $value->color; 
                $tmp[$value->color]['color'] = $value->colorname;   
                $tmp[$value->color]['size'][] = array(
                    'id' =>  $value->size,
                    'title'=> $value->sizename,
                    'qty' =>(int)$value->stock,
                    'price' =>(float)$value->price,
                );
                $tmp[$value->color]['image'][] = 
                    $value->variant_image;
                // $tmp[$value->color]['size'][$key]['title'] = $value->sizename; 
                // $tmp[$value->color]['size'][$key]['qty'] = $value->stock; 
            }
            else
            {
                $tmp[$value->color]['id'] = $value->color; 
                $tmp[$value->color]['color'] = $value->colorname; 
                 $tmp[$value->color]['size'][] = array(
                    'id' =>  $value->size,
                    'title'=> $value->sizename,
                    'qty' =>(int)$value->stock,
                    'price' =>(float)$value->price,
                );
                  $tmp[$value->color]['image'][] = 
                   $value->variant_image;
                // $tmp[$value->color]['size'][$key]['id'] = $value->size; 
                // $tmp[$value->color]['size'][$key]['title'] = $value->sizename; 
                // $tmp[$value->color]['size'][$key]['qty'] = $value->stock; 
            }           
        }

        $final_tmp = array();
        foreach ($tmp as $value) 
        {
            $value['image'][] =   $products[0]->product_image;
            $final_tmp[] = $value;
        }

        $products[0]['variant'] = $final_tmp;
        $products[0]['relatedProducts'] = $relatedProducts;
      
        if($products->count()>0)
        {
            unset($products[0]->updated_at,$products[0]->deleted_at);
            $success = $products[0];
            
           // $success['variant'] = $tmp;
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
        $price = $request->price;
        $color= $request->color;
        $size = $request->size;

        $cartData = Cart::where(['product_id'=>$product_id,'user_id'=>$user_id,'color'=>$color,'size'=>$size])->first();
        $cartcount = Cart::where('user_id',$user_id)->count();
        if($cartData)
        {
            $cartData->quantity = $cartData->quantity + $quantity;
            Cart::unguard();
            $success = $cartData->save();
            $response = ['success' => true,'status'=> 200,'message' => 'Item quantity updated','cartcount'=> $cartcount ];
        }
        else
        {
            $cart = array(
                'user_id'=>$user_id,
                'product_id'=>$product_id,
                'quantity'=>$quantity,
                'price'=>$price,
                'color'=>$color,
                'size'=>$size
            );
            Cart::unguard();
            $success = Cart::create($cart);
            $cartcount = Cart::where('user_id',$user_id)->count();
            if($success)
            {
                $response = ['success' => true,'status' => 200,'message' => 'Item Added to Cart  Successfully.','cartcount'=> $cartcount ,'data'=>$success];
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
            $cartData = Cart::find($value['cart_id']);
            if($cartData)
            {
                $cartData->quantity = $value['quantity'];
                // $cartData->color = $value['color'] ;
                // $cartData->size = $value['size'];
                // $cartData->price = $value['price'];
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
        $cart_id = $request->cart_id;

        $affectedRows = Cart::where(['id'=>$cart_id])->delete();

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

    public function getCart(Request $request)
    {
        $url =env('APP_URL');

        $user_id = $request->user_id;

        $getRows = Cart::select('cart.id','cart.user_id','cart.product_id','cart.quantity','products.name','products.stock as availablequantity','products.price',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"),'size.size','colors.color')->join('products','products.id','=','cart.product_id')->leftjoin('colors','colors.id','=','cart.color')->leftjoin('size','size.id','=','cart.size')->where(['user_id'=>$user_id])->get();

        $total = DB::table('cart')->select(DB::raw('sum(quantity * price) as total'))->where('user_id',$user_id)->get();
        if($getRows)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Item found Successfully.','total'=>$total[0]->total,'data'=>$getRows];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'Item not deleted'];
        }
        return response()->json($response);
    }

}
