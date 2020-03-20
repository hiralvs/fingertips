<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use \Maatwebsite\Excel\Exporter;
use App\Brand;
use App\User;
use App\Category;
use Datatables;
use Excel;
use App\Exports\UserExport;
use Validator;

class BrandController extends Controller
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
        $return_data['title'] = trans('Brand Listing');
        $return_data['meta_title'] = trans('Brand Listing');

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
        $return_data['data'] = brand::orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['category_id'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['grand_merchant_user_id'] = User::select('id', 'name')->where('role','brand_merchant')->get();
        return View('admin.brand.index', $return_data)->render();
    }

    public function addbrand(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'status' => 'required',
            'commission' => 'required',
        ]);
        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id('brands');
        if ($request->hasFile('brand_image')) {

            $image = $request->File('brand_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/brands/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['brand_image'] = $filename;
        }
        $check = Brand::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Brand::find($check);
        
        $arr = array('msg' => 'Brand Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request){
        $query = Brand::where('id',$request->id);
        $query->delete();
        return redirect()->route('brand')->with('success', 'Brand Deleted Successfully');
    }

    public function edit(Request $request){
        $query = Brand::where('id',$request->id);
        return View('admin.brand.edit',$query)->render();
    }
 public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'status' => 'required',
            'commission' => 'required',
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->grand_merchant_user_id = $request->grand_merchant_user_id;
        $brand->category_id = $request->category_id;
        $brand->status = $request->status;
        $brand->commission = $request->commission;
    
        if ($request->hasFile('brand_image')) {

            $image = $request->File('brand_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $brand->brand_image = $filename;
        }
        $brand->save();
       
        if (!empty($brand)) {
            $data = Brand::find($request->id);
            $arr = array('msg' => 'Brand Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
    public function search(Request $request)
    {
        $search = $request->input('search');

        $brand = Brand::where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")->paginate();

        if($brand)
        {
            $arr = array('status' => true,"data"=>$brand[0]);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }
        return Response()->json($arr);
    }

}