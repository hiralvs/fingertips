<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\User;
use App\Brand;
use DB;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /* Function user to display user list */
    public function index(Request $request) 
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Product Listing');
        $return_data['meta_title'] = trans('Product Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }
        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='id';
        }
        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        $return_data['data'] = Product::orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['brands'] = Brand::select('id', 'name')->get();
        // echo "<pre>";
        // print_r($return_data['category_id']);
        // exit;
        return View('admin.product.index', $return_data)->render();
    }

    /* Function used to add shops */
    public function addProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skuid' => 'required',
            'name' => 'required|max:255',
            'category' => 'required',
            'brand' => 'required',
            'price' => 'required',
            'price' => 'required'
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $input = array(
            'unique_id' =>  get_unique_id("products"),
            'brand_id' => $request->brand,
            'sku_id' => $request->skuid,
            'name' => $request->name,
            'category_id' =>  implode(",",$request->category),
            'price' =>  $request->price,
            'stock' =>  isset($request->stock) ? $request->stock : '',
            'gender' =>  isset($request->gender) ? $request->gender :'' ,
            'type' =>  isset($request->type) ? $request->type :'' ,
            'color' =>  isset($request->color) ? $request->color :'' ,
            'material' =>  isset($request->material) ? $request->material :'' ,
            'status' =>  $request->status,
            'description' => isset($request->description) ? $request->description : ''
        );  

        if ($request->hasFile('product_image')) {

            $image = $request->File('product_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/products/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['product_image'] = $filename;
        }
        Product::unguard();
        $check = Product::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data['products'] = Product::find($check);
        
        $arr = array('msg' => 'Products Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
}
