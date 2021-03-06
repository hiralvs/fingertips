<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
// use \Maatwebsite\Excel\Exporter;
use App\Brand;
use App\User;
use App\Category;
use App\Product;
use Datatables;
use DB;
// use Excel;
// use App\Exports\UserExport;
use Validator;

class BrandController extends Controller
{
    public function __construct(\Maatwebsite\Excel\Exporter $excel)
    {
        $this->middleware('auth');
        // $this->excel = $excel;
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
        $return_data['data'] = Brand::select('brands.*',DB::raw("(SELECT COUNT(products.id) FROM products WHERE products.brand_id = brands.id) as product_count"),DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('category',DB::raw("FIND_IN_SET(category.id,brands.category_id)"),">",DB::raw("'0'"))->groupBy("brands.id")->orderBy($sort,$direction)->toSql();//sortable()->paginate($perpage);
        print_r($return_data['data']);
        
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        
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
        $request->request->remove('desc');

        $input = $request->all();
        $input['unique_id'] =  get_unique_id('brands');
        $input['category_id'] =  implode(",", $input['category_id']);

        if ($request->hasFile('brand_image')) {

            $image = $request->File('brand_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/brands/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['brand_image'] = $filename;
        }
        Brand::unguard();
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
        $categoryid = implode(",",$request->category_id);
        $brand->name = $request->name;
        $brand->grand_merchant_user_id = $request->grand_merchant_user_id;
        $brand->category_id =  $categoryid;
        $brand->status = $request->status;
        $brand->commission = $request->commission;
        $brand->description =  $request->description;
        if ($request->hasFile('brand_image')) {

            $image = $request->File('brand_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $brand->brand_image = $filename;
        }
        Brand::unguard();
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

        $brand = Brand::select('brands.*',DB::raw("(SELECT COUNT(products.id) FROM products WHERE products.brand_id = brands.id) as product_count"),DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('category',DB::raw("FIND_IN_SET(category.id,brands.category_id)"),">",DB::raw("'0'"))->where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")->orWhere('category_name', 'LIKE',"%{$search}%")->groupBy("brands.id")->paginate();

        if($brand)
        {
            $html = $image ="";
            foreach ($brand as $key => $value) {
                if($value['brand_image'] != null) 
                {
                    $path = asset('public/upload/brands').'/'.$value->brand_image;
                   $image = '<img src="'.$path.'" alt="">';
                }

                if($value['status'] == '1') 
                {
                    $status = "Inactive";
                }
                else{
                    $status = "Active";            
                }
                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr><td>".$image."</td><td>".$value['unique_id']."</td>
                <td>".$value['name']."</td>
                            <td>".$value['product_count']."</td>
                            <td>".$value['category_name']."</td>
                            <td>".$value['commission']."'</td>
                            <td>".$status."</td>
                            <td>". $cdate ."</td>
                            <td><a class='edit open_modal' data-toggle='modal' data-target='#editBrand".$value['id']."''><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Brand?')' href=".route('brand.delete', $value->id)."><i class='mdi mdi-delete'></i></a> </td>
                        </tr>";
            }
            $arr = array('status' => true,"data"=>$html);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }
        return Response()->json($arr);
    }

    public function export(Request $request)
    {
        $search = (isset($request->search) && $request->search !="") ? $request->search : "";
        $query = Brand::select('brands.*',DB::raw("(SELECT COUNT(products.id) FROM products WHERE products.brand_id = brands.id) as product_count"),DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('category',DB::raw("FIND_IN_SET(category.id,brands.category_id)"),">",DB::raw("'0'"))->where('brands.status','0')->groupBy("brands.id");

        if($request->search != "")
        {
            $query = $query->where('name','LIKE',"%{$search}%")
            ->orWhere('unique_id', 'LIKE',"%{$search}%");
        }

        $finaldata = $query->get();
        $html = "";
        if(!empty($finaldata) && $finaldata->count() > 0)
        {       

            $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                          <th>Brand Logo</th>
                          <th>Id</th>
                          <th>Name</th>
                          <th>No Of Products</th>
                          <th>Category</th>
                          <th>Total Earnings</th>
                          <th>Status</th>
                          <th>Created on</th>
                        </tr>
                      </thead>
                      <tbody>';     
            foreach ($finaldata as $key => $value) 
            {
               
                if($value['status'] == '1') 
                {
                    $status = "Inactive";
                }
                else{
                    $status = "Active";            
                }
                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr><td>".$value['brand_image']."</td><td>".$value['unique_id']."</td>
                <td>".$value['name']."</td>
                            <td>".$value['product_count']."</td>
                            <td>".$value['category_name']."</td>
                            <td>".$value['commission']."'</td>
                            <td>".$status."</td>
                            <td>". $cdate ."</td>
                        </tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="10">No Records Found</td></tr>';
        }
        $html .= '</tbody></table>';
        echo $html;
    }

}