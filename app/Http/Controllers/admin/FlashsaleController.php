<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\FlashSale;
use App\Product;
use DB;
use Validator;

class FlashsaleController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $user = Auth::user();
    }
    
    /* Function used to display trending */
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Flashsale');
        $return_data['meta_title'] = trans('Flashsale');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='flash_sales.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        $return_data['data'] = FlashSale::select('flash_sales.*','brands.name as brandname')->leftjoin('products','products.id','=','flash_sales.product_id')->leftjoin('brands','brands.id','=','products.brand_id')->orderBy('id', 'desc')->sortable()->paginate($perpage);
        $return_data['product'] = Product::select('id','name')->get();        
        return View('admin.flashsale.index', $return_data)->render();
    }

     /* Function used to add shops */
    public function addflashsale(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'product' => 'required',
            'discount_percentage' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required'
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $request->request->remove('_token');
        $productname = Product::find($request->product);
        $input = array(
            'unique_id' => get_unique_id("flash_sales") ,
            'name' => $request->discount_percentage."% off ".$productname->name,
            'product_id' => $request->product ,
            'discount_percentage' => $request->discount_percentage ,
            'discounted_price' => ($productname->price - $productname->price*$request->discount_percentage /100),
            'start_date' => date('Y-m-d',strtotime($request->start_date)) ,
            'start_time' => $request->start_time ,
            'end_date' => date('Y-m-d',strtotime($request->end_date)),
            'end_time' => $request->end_time,
            'quantity' => $request->quantity,
        );

        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/flashsale/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['image'] = $filename;
        }
        FlashSale::unguard();
        $check = FlashSale::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
       
        $arr = array('msg' => 'Flash Sale Added Successfully', 'status' => true,'data'=> []);
        }
        return Response()->json($arr);
    }

    /* Function used to delete shops */
    public function delete(Request $request)
    {
        $query = FlashSale::where('id',$request->id);
        $query->delete();
        return redirect()->route('flashsale')->with('success', 'Flashsale Deleted Successfully');
    }

    /* Function used to update shops */
    public function update(Request $request)
    {
        $flashsale = FlashSale::find($request->id);

        $validator = Validator::make($request->all(), [
            'product' => 'required',
            'discount_percentage' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'end_date' => 'required',
            'end_time' => 'required'
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $productname = Product::find($request->product);

        $flashsale->name =  $request->discount_percentage."% off ".$productname->name;
        $flashsale->product_id =  $request->product;
        $flashsale->discount_percentage =  $request->discount_percentage;
        $flashsale->discounted_price =  ($productname->price - $productname->price*$request->discount_percentage /100);
        $flashsale->start_date =  date('Y-m-d',strtotime($request->start_date));
        $flashsale->start_time =  $request->start_time;
        $flashsale->end_date =  date('Y-m-d',strtotime($request->end_date));
        $flashsale->end_time =  $request->end_time;
        $flashsale->quantity =  $request->quantity;
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/flashsale/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $flashsale->image = $filename;
        }
        FlashSale::unguard();
        $flashsale->save();
       
        if (!empty($flashsale)) {
            $arr = array('msg' => 'FlashSale Updated Successfully', 'status' => true,'data'=>[]);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }

    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $flashsale = FlashSale::select('flash_sales.*','brands.name as brandname')->leftjoin('products','products.id','=','flash_sales.product_id')->leftjoin('brands','brands.id','=','products.brand_id')->where('brands.name','LIKE',"%{$search}%")
        ->orWhere('flash_sales.name','LIKE',"%{$search}%")
        ->orWhere('discount_percentage', 'LIKE',"%{$search}%")
        ->orWhere('discounted_price', 'LIKE',"%{$search}%")
        ->orWhere('start_date', 'LIKE',"%{$search}%")
        ->orWhere('start_time', 'LIKE',"%{$search}%")
        ->orWhere('end_date', 'LIKE',"%{$search}%")
        ->orWhere('end_time', 'LIKE',"%{$search}%")->paginate();

        if($flashsale)
        {
        $data = $this->htmltoexportandsearch($flashsale,true);
        $arr = array('status' => true,"data"=>$data);    
        }
        else{
         $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }

        return Response()->json($arr);

    }

    public function export(Request $request)
    {
        $search = (isset($request->search) && $request->search !="") ? $request->search : "";
        $query = FlashSale::select('flash_sales.*','brands.name as brandname')->leftjoin('products','products.id','=','flash_sales.product_id')->leftjoin('brands','brands.id','=','products.brand_id');

        if($request->search != "")
        {
            $query = $query->where('brands.name','LIKE',"%{$search}%")
        ->orWhere('flash_sales.name','LIKE',"%{$search}%")
        ->orWhere('discount_percentage', 'LIKE',"%{$search}%")
        ->orWhere('discounted_price', 'LIKE',"%{$search}%")
        ->orWhere('start_date', 'LIKE',"%{$search}%")
        ->orWhere('start_time', 'LIKE',"%{$search}%")
        ->orWhere('end_date', 'LIKE',"%{$search}%")
        ->orWhere('end_time', 'LIKE',"%{$search}%");
        }

        $finaldata = $query->get();
        $this->htmltoexportandsearch($finaldata);
       
    }

    public function htmltoexportandsearch($finaldata,$search=false)
    {
        $html = "";
        if(!empty($finaldata) && $finaldata->count() > 0)
        {   
            if($search==false)
            {
                  $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Brand Name</th>
                            <th>Start Date</th>
                            <th>Start Time</th>
                            <th>End Date</th>
                            <th>End Time</th>
                            <th>Discount Percentage</th>
                            <th>Created at</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr><td>".$value['unique_id']."</td><td>".$value['name']."</td><td>".$value['brandname'] ."</td><td>".$value['start_date']."</td><td>".$value['start_time']."</td><td>".$value['end_date']."</td><td>".$value['end_time']."</td><td>".$value['discount_percentage']."</td><td>".$cdate."</td>";
                if($search == true)
                {
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editFlashsales".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Flash Sale?')' href='".route('flashsale.delete', $value->id)."'><i class='mdi mdi-delete'></i></a> 
                          </td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="10">No Records Found</td></tr>';
        }
        if($search==false)
        {
            $html .= '</tbody></table>';
            echo $html;
        }
        else
        {
            return $html;
        }
        
    }

}