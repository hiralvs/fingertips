<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Product;
use App\Category;
use App\User;
use App\Brand;
use App\ProductVariant;
use DB;
use Validator;


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
        //DB::raw("FIND_IN_SET(tags.id,myposts.tags)");
        $return_data['data'] = Product::select('products.*',DB::raw("GROUP_CONCAT(category_name) as category_name"),DB::raw("(SELECT COUNT(product_variant.id) FROM product_variant WHERE product_variant.product_id = products.id) as productvariantcount"))->leftjoin('category',DB::raw("FIND_IN_SET(category.id,products.category_id)"),">",DB::raw("'0'"))->groupBy("products.id")->orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['brands'] = Brand::select('id', 'name')->get();
        
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
            'status' => 'required'
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
            'sizefit'=> isset($request->sizeandfit) ? $request->sizeandfit : '',
            'type' =>  isset($request->type) ? $request->type :'' ,
            'color' =>  isset($request->color) ? $request->color :'' ,
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

    /* Function used to delete record*/
    public function delete(Request $request){
        $query = Product::where('id',$request->id);
        $query->delete();
        return redirect()->route('products')->with('success', 'Product Deleted Successfully');
    }

    /* Function used to update record*/
    public function update(Request $request)
    {
        $product = Product::find($request->id);

        $validator = Validator::make($request->all(), [
            'skuid' => 'required',
            'name' => 'required|max:255',
            'category' => 'required',
            'brand' => 'required',
            'price' => 'required',
            'status' => 'required'
        ]);
        
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $product->sku_id = $request->skuid;
        $product->brand_id = $request->brand;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = implode(",",$request->category);
        $product->stock = isset($request->stock) ? $request->stock : '';
        $product->sizefit = isset($request->sizefit) ? $request->sizefit : '';
        $product->type = isset($request->type) ? $request->type : '';
        $product->color = isset($request->color) ? $request->color : '';
        $product->status = $request->status;
        $product->description = isset($request->description) ? $request->description : '';
    
        if ($request->hasFile('product_image')) {

            $image = $request->File('product_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/products/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $product->product_image = $filename;
        }
        $product->save();
       
        if (!empty($product)) {
            $data = Product::find($request->id);
            $arr = array('msg' => 'Product Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
    public function search(Request $request)
    {
        $search = $request->input('search');
        $product = Product::select('products.*',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('category',DB::raw("FIND_IN_SET(category.id,products.category_id)"),">",DB::raw("'0'"))->where('products.name','LIKE',"%{$search}%")
        ->orWhere('products.unique_id', 'LIKE',"%{$search}%")->orWhere('category_name', 'LIKE',"%{$search}%")->groupBy("products.id")->paginate();
        
        //$product = Product::where('name','LIKE',"%{$search}%")->orWhere('unique_id', 'LIKE',"%{$search}%")->paginate();

        if($product)
        {
            $arr = array('status' => true,"data"=>$product[0]);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }
        return Response()->json($arr);
    }

    public function product_variant(Request $request)
    {
       $id = $request->id;
       $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Product Variant');
        $return_data['meta_title'] = trans('Product Variant');

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
        $return_data['id'] =  $id;
        //DB::raw("FIND_IN_SET(tags.id,myposts.tags)");
        $return_data['data'] = ProductVariant::where('product_id',$id)->orderBy($sort,$direction)->sortable()->paginate($perpage);
       //echo "<pre>"; print_r($return_data['data']);
        return View('admin.productvariant.index', $return_data)->render();
    }

    /* Function used to add shops */
    public function addProductsVariant(Request $request)
    {
        $input = array(
            'unique_id' =>  get_unique_id("product_variant"),
            'product_id' => $request->pid,
            'variant_name' => $request->variant_name,
            'price' =>  $request->price,
            'stock' =>  isset($request->stock) ? $request->stock : '',
            'size'=> isset($request->size) ? $request->size : '',
        );  

        if ($request->hasFile('variant_image')) {

            $image = $request->File('variant_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/products/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['variant_image'] = $filename;
        }
        ProductVariant::unguard();
        $check = ProductVariant::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data['products'] = ProductVariant::find($check);
        
        $arr = array('msg' => 'Product Variant Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

     /* Function used to delete record*/
    public function variantdelete(Request $request){
        $productsvariant = ProductVariant::find($request->id);
        $pid = $productsvariant->product_id;
        $query = ProductVariant::where('id',$request->id);
        $query->delete();

        return redirect()->route('products_variant',$pid)->with('success', 'Product Variant Deleted Successfully');
    }

     /* Function used to update record*/
    public function variantupdate(Request $request)
    {
        $productvar = ProductVariant::find($request->id);

        $productvar->variant_name = $request->variant_name;
        $productvar->price = $request->price;
        $productvar->stock = isset($request->stock) ? $request->stock : '';
        $productvar->size = isset($request->size) ? $request->size : '';

        if ($request->hasFile('variant_image')) {

            $image = $request->File('variant_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/products/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $productvar->variant_image = $filename;
        }
        $productvar->save();
       
        if (!empty($productvar)) {
            $data = ProductVariant::find($request->id);
            $arr = array('msg' => 'Product Variant Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }

    public function variantsearch(Request $request)
    {
        $search = $request->input('search');
        $product = ProductVariant::where('variant_image','LIKE',"%{$search}%")
        ->orWhere('variant_name', 'LIKE',"%{$search}%")->orWhere('stock', 'LIKE',"%{$search}%")->orWhere('price', 'LIKE',"%{$search}%")->paginate();
        
        if($product)
        {
            $arr = array('status' => true,"data"=>$product[0]);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }
        return Response()->json($arr);
    }
}
