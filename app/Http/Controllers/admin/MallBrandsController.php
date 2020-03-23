<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Maatwebsite\Excel\Exporter;
use App\Brand_Connection;
use App\ShopsandMalls;
use App\User;
use App\Brand;
use Datatables;
use Excel;
use App\Exports\UserExport;
use Validator;

class MallBrandsController extends Controller
{
    public function __construct(\Maatwebsite\Excel\Exporter $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Mall Brands');
        $return_data['meta_title'] = trans('Mall Brands');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }
        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='id';
        }
        // if ($request->direction) {
        //     $direction=$request->direction;
        // } else {
        //     $direction='desc';
        // }
        $return_data['data'] = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->where('type','malls')->orderBy('id', 'desc')->sortable()->paginate($perpage);
        $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
        $return_data['brand_id'] = Brand::select('id', 'name')->orderBy('name', 'asc')->get();
        return View('admin.mallbrands.index', $return_data)->render();
    }
        /* Function used to add  */
    public function addMallBrand(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            // 'name' => 'required|max:255',
            'brand_id' => 'required',
            //'closinghrs' => 'required|after:openinghrs',
            'common_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $username = "";
        if(!empty($user))
        {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $request->request->remove('desc');
        $input = $request->all();
        $input['type'] = 'malls';
        $input['unique_id'] =  get_unique_id("brands_connection");

        Brand_Connection::unguard();
        $check = Brand_Connection::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data['malls'] = Brand_Connection::find($check);
        // $data['brand_id'] = Brand::select('name as brandname')->find($data['malls']->property_admin_user_id);
        // $data['propertyadmin'] = User::select('name as propertyadmin')->find($data['malls']->property_admin_user_id);
        $arr = array('msg' => 'MallBrand Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request){
        $query = Brand_Connection::where('id',$request->id);
        $query->delete();
        return redirect()->route('mallbrands')->with('success', 'MallBrand Deleted Successfully');
    }
    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'brand_id' => 'required',
            'common_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }


        $brand = Brand_Connection::find($request->id);
        $brand->brand_id = $request->brand_id;
        $brand->common_id = $request->common_id;
        $brand->status = $request->status;
        $brand->save();
       
        if (!empty($brand)) {
            $data = Brand_Connection::find($request->id);
            $arr = array('msg' => 'Brand Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
    // public function search(Request $request)
    //  {
    //     $search = $request->input('search');

    //     $rewards = Brand_Connection::select('brands_connection.*', 'brands.id','brands.name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->where('brands.name','LIKE',"%{$search}%")
    //     //  ->orWhere('unique_id', 'LIKE',"%{$search}%")   
    //     //  ->orWhere('contact', 'LIKE',"%{$search}%")
    //     //  ->orWhere('featured_event', 'LIKE',"%{$search}%")
    //      ->paginate();

        
 
    //     if($rewards)
    //      {
    //          $arr = array('status' => true,"data"=>$rewards[0]);    
    //      }
    //      else{
    //          $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
    //      }
 
    //      return Response()->json($arr);
 
    //  }
 public function search(Request $request)
     {
         $search = $request->input('search');
 
        //  $brandconnection = Brand_Connection::select('brands_connection.*','brands.id as brandid','brands.name as brandname')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->where('brands_connection.brand_id','LIKE',"%{$search}%") 

$brandconnection = Brand_Connection::select('brands_connection.*','brands.id as brandid','brands.name as brandname')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->where('brands.name','LIKE',"%{$search}%")->orWhere('brands_connection.unique_id','=',"%{search}%")

         // ->orWhere('unique_id', 'LIKE',"%{$search}%")
        ->orWhere('brand_id', 'LIKE',"%{$search}%")
        // ->orWhere('status', 'LIKE',"%{$search}%")
         ->paginate();
         if($brandconnection)
         {
             $arr = array('status' => true,"data"=>$brandconnection[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }


}