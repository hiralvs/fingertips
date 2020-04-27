<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Maatwebsite\Excel\Exporter;
use App\Brand_Connection;
use App\ShopsandMalls;
use App\Events;
use App\Attractions;
use App\User;
use App\Brand;
use Validator;

class CommonBrandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $lastsegment = request()->segment(count(request()->segments()));

        $auth = Auth::user();
        $return_data = array();
       
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
        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        $return_data['brand_id'] = Brand::select('id', 'name')->orderBy('name', 'asc')->get();

        if($lastsegment == 'eventbrands')
        {
            $return_data['title'] = trans('Event Brands');
            $return_data['meta_title'] = trans('Event Brands');
            $return_data['data'] = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','event_name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('events', 'events.id', '=', 'brands_connection.common_id')->where('brands_connection.type','event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['events'] = Events::select('id', 'event_name')->get();
            return View("admin.eventbrands.index", $return_data)->render(); 
        }
        else if($lastsegment == 'mallbrands')
        {
            $return_data['title'] = trans('Mall Brands');
            $return_data['meta_title'] = trans('Mall Brands');
            $return_data['data'] = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','shopsandmalls.name as mallname')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'brands_connection.common_id')->where('brands_connection.type','malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallbrands.index', $return_data)->render(); 
        }
        else if($lastsegment == 'attractionbrands')
        {
            $return_data['title'] = trans('Attraction Brands');
            $return_data['meta_title'] = trans('Attraction Brands');
            $return_data['data'] = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','attraction_name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('attractions', 'attractions.id', '=', 'brands_connection.common_id')->where('brands_connection.type','attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['attraction'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionbrands.index', $return_data)->render(); 
        }
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
        //$input['type'] = 'malls';
        $input['unique_id'] =  get_unique_id("brands_connection");

        Brand_Connection::unguard();
        $check = Brand_Connection::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
            $arr = array('msg' => 'Brands Added Successfully', 'status' => true);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request){
        $lastsegment = request()->segments();
        if($lastsegment[0] == 'mallbrandsdelete')
        {
            $lastsegment = 'mallbrands';
        }
        if($lastsegment[0] == 'eventbrandsdelete')
        {
            $lastsegment = 'eventbrands';
        }
        if($lastsegment[0] == 'attractionbrandsdelete')
        {
            $lastsegment = 'attractionbrands';
        }


        $query = Brand_Connection::where('id',$request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'Brand Deleted Successfully');
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
   
    public function search(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        if($type == 'attraction')
        {
            $brandconnection = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','attraction_name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('attractions', 'attractions.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('attractions.attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'malls')
        {
            $brandconnection = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','shopsandmalls.name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'malls');
                })->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'event')
        {
            $brandconnection = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','event_name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('events', 'events.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'event');
                })->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%");
                })->paginate();
        }        

         if($brandconnection)
         {
            $data = $this->htmltoexportandsearch($brandconnection,$type,true);
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
        $lastsegment = $request->type;
        if ($lastsegment == 'event') 
        {
            $query = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','event_name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('events', 'events.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'event');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%"); 
                });
            }
        } 
        elseif ($lastsegment == 'malls') 
        {
            $query = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','shopsandmalls.name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'malls');
                });

            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                     });
            }
            
        } 
        elseif ($lastsegment == 'attraction') 
        {
           $query = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname','attraction_name as name')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->leftjoin('attractions', 'attractions.id', '=', 'brands_connection.common_id')->where(function ($query) {
                $query->where('brands_connection.type', 'attraction');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('brands_connection.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                     }); 
            }
        }

        $finaldata = $query->get();
        $this->htmltoexportandsearch($finaldata,$lastsegment);
       
    }

    public function htmltoexportandsearch($finaldata,$type,$search=false)
    {
        if($type == 'malls')
        {
            $th = 'Shops and Malls Name';
            $deleteroute = "mallbrands.delete";
        }
        else if($type=='event')
        {
            $th = 'Event Name';
            $deleteroute = "eventbrands.delete";
        }
        elseif ($type == 'attraction') {
             $th = 'Attraction Name';
             $deleteroute = "attractionbrands.delete";
        }
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
                            <th>'. $th.'</th>
                            <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                
                $html .="<tr><td>".$value['unique_id']."</td><td>".$value['brandname'] ."</td><td>".$value['name']."</td><td>".$value['status']."</td>";
                if($search == true)
                {                    
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editMallBrands".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Brand?')' href='".route($deleteroute , $value->id)."'><i class='mdi mdi-delete'></i></a></td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="5">No Records Found</td></tr>';
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