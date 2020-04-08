<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Map_images;
use App\Attractions;
use App\Category;
use App\Events;
use App\Settings;
use App\ShopsandMalls;
use DB;
use Validator;

class CommonMapImageController extends Controller
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
        $lastsegment = request()->segment(count(request()->segments()));

        $auth = Auth::user();
        // $return_data = array();
        $return_data['title'] = trans('MapImage List');
        $return_data['meta_title'] = trans('MapImage List');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='map_images.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        if ($lastsegment == 'eventmapimage') {
            $return_data['title'] = trans('Event MapImage List');
            $return_data['meta_title'] = trans('Event MapImage List');
            $return_data['data'] = Map_images::select('map_images.*', 'event_name')->leftjoin('events', 'events.id', '=', 'map_images.common_id')->where('map_images.type', 'event')->orderBy($sort, $direction)->sortable()->paginate($perpage);

            $return_data['common_id'] = Events::select('id', 'event_name')->get();
            return View('admin.eventmapimage.index', $return_data)->render();
        }
         else if ($lastsegment == 'mallmapimage') {
            $return_data['title'] = trans('Mall MapImage');
            $return_data['meta_title'] = trans('Mall MapImage');
            $return_data['data'] = Map_images::select('map_images.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'map_images.common_id')->where('map_images.type', 'malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallmapimage.index', $return_data)->render();
        } else if ($lastsegment == 'attractionmapimage') {
            $return_data['title'] = trans('Attraction MapImage List');
            $return_data['meta_title'] = trans('Attraction MapImage List');
            $return_data['data'] = Map_images::select('map_images.*', 'attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'map_images.common_id')->where('map_images.type', 'attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionmapimage.index', $return_data)->render();
        }
    }
    public function addMapImage(Request $request)
    {
        if ($request->type == 'malls') {
            $validator = Validator::make($request->all(), [
                'mallname' => 'required',
                'map_image_name' => 'required|image',
            ]);

            $common_name =  $request->mallname;
        }

        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'eventname' => 'required',
            ]);

            $common_name =  $request->eventname;
        }

        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
                'map_image_name' => 'required',
                'attractionname' => 'required',
            ]);

            $common_name =  $request->attractionname;
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        
        $user = Auth::user();
        $username = "";
        if (!empty($user)) {
            $username = $user->name;
        }

        $request->request->remove('_token');
        $input = array(
            'unique_id' => get_unique_id("map_images"),
            'common_id'=>$common_name,
            'type'=>  $request->type,
        );
        
        if ($request->hasFile('map_image_name')) {
            $image = $request->File('map_image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            $path = public_path('upload/mall_image/' . $filename);
            
            Image::make($image->getRealPath())->save($path);
            $input['map_image_name'] = $filename;
        }
        Map_images::unguard();
        $check = Map_images::create($input)->id;
    
        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Map_images::find($check);
            $arr = array('msg' => 'Map Image Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $lastsegment = request()->segments();
        if ($lastsegment[0] == 'mallmapimagedelete') {
            $lastsegment = 'mallmapimage';
        }
        if ($lastsegment[0] == 'eventmapimagedelete') {
            $lastsegment = 'eventmapimage';
        }
        if ($lastsegment[0] == 'attractionmapimagedelete') {
            $lastsegment = 'attractionmapimage';
        }

        $query = Map_images::where('id', $request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'MapImage Deleted Successfully');
    }
    public function update(Request $request)
    {
        $mapimages = Map_images::find($request->id);

        if ($request->type == 'malls') {
            $validator = Validator::make($request->all(), [
                'mallname' => 'required',
            ]);
            $common_name =  $request->mallname;
        }
        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'eventname' => 'required',
            ]);
            $common_name =  $request->eventname;
        }
        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
            'attractionname' => 'required',
            ]);
            $common_name =  $request->attractionname;
        }

        if ($mapimages->notHavingImageInDb()) {
            $rules['map_image_name'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $mapimages->common_id = $common_name ;
        $mapimages->type = $request->type;        
        if ($request->hasFile('map_image_name')) {
            $image = $request->File('map_image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/mall_image/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $mapimages->map_image_name = $filename;
        }
        Map_images::unguard();
        $mapimages->save();
       
        if (!empty($mapimages)) {
            $data = Map_images::find($request->id);
            $arr = array('msg' => 'Map images Updated Successfully', 'status' => true,'data'=> $data);
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
            $map_images = Map_images::select('map_images.*', 'attractions.id as attractionid','attractions.attraction_name as name')
            ->leftjoin('attractions', 'map_images.common_id', '=', 'attractions.id')
            // ->leftjoin('attractions', 'attractions.id', '=', 'brands_connection.common_id')
            ->where(function ($query) {
                $query->where('map_images.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->where('attractions.attraction_name','LIKE',"%{$search}%")
                    ->orWhere('map_images.unique_id','LIKE',"%{search}%")
                    ->orWhere('common_id', 'LIKE',"%{$search}%");
                })->paginate();
            }        
            if($type == 'event')
            {
                $map_images = Map_images::select('map_images.*', 'events.id as eventid','events.event_name as name')
                ->leftjoin('events', 'map_images.common_id', '=', 'events.id')
                         ->where(function ($query) {
                    $query->where('map_images.type', 'event');
                    })->where(function ($query)   use ($search){
                        $query->where('events.event_name','LIKE',"%{$search}%")
                        ->orWhere('map_images.unique_id','LIKE',"%{search}%")
                        ->orWhere('common_id', 'LIKE',"%{$search}%");
                    })->paginate();
            }
            if($type == 'malls')
            {
                $map_images = Map_images::select('map_images.*', 'shopsandmalls.id as mallid','shopsandmalls.name as name')
                ->leftjoin('shopsandmalls', 'map_images.common_id', '=', 'shopsandmalls.id')
                // ->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'brands_connection.common_id')
                ->where(function ($query) {
                    $query->where('map_images.type', 'malls');
                    })->where(function ($query)   use ($search){
                        $query->where('shopsandmalls.name','LIKE',"%{$search}%")
                        ->orWhere('common_id', 'LIKE',"%{$search}%")
                        ->orWhere('map_images.unique_id','LIKE',"%{search}%");
                        // ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    })->paginate();
            }
         if($map_images)
         {
            $data = $this->htmltoexportandsearch($map_images,$type,true);
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
            $query =Map_images::select('map_images.*', 'events.id as eventid','events.event_name as name')->leftjoin('events', 'map_images.common_id', '=', 'events.id')->where(function ($query) {
                    $query->where('map_images.type', 'event');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                        $query->where('events.event_name','LIKE',"%{$search}%")
                        ->orWhere('map_images.unique_id','LIKE',"%{search}%")
                        ->orWhere('common_id', 'LIKE',"%{$search}%"); 
                });
            }
        } 
        elseif ($lastsegment == 'malls') 
        {
            $query = Map_images::select('map_images.*', 'shopsandmalls.id as mallid','shopsandmalls.name as name')->leftjoin('shopsandmalls', 'map_images.common_id', '=', 'shopsandmalls.id')
                ->where(function ($query) {
                    $query->where('map_images.type', 'malls');
            });

            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                        $query->where('shopsandmalls.name','LIKE',"%{$search}%")
                        ->orWhere('common_id', 'LIKE',"%{$search}%")
                        ->orWhere('map_images.unique_id','LIKE',"%{search}%");
                     });
            }
            
        } 
        elseif ($lastsegment == 'attraction') 
        {
           $query = Map_images::select('map_images.*', 'attractions.id as attractionid','attractions.attraction_name as name')
            ->leftjoin('attractions', 'map_images.common_id', '=', 'attractions.id')
            ->where(function ($query) {
                $query->where('map_images.type', 'attraction');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('attractions.attraction_name','LIKE',"%{$search}%")
                    ->orWhere('map_images.unique_id','LIKE',"%{search}%")
                    ->orWhere('common_id', 'LIKE',"%{$search}%");
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
            $deleteroute = "mallmapimage.delete";
        }
        else if($type=='event')
        {
            $th = 'Event Name';
            $deleteroute = "eventmapimage.delete";
        }
        elseif ($type == 'attraction') {
             $th = 'Attraction Name';
             $deleteroute = "attractionmapimage.delete";
        }
        $html = "";
        if(!empty($finaldata) && $finaldata->count() > 0)
        {   
            if($search==false)
            {
                  $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                            <th>Event Map Image</th>
                            <th>Map Image id</th>
                            <th>'. $th.'</th>
                            <th>Created On</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                if($search == true)
                {
                    if($value['map_image_name']!= null)
                    {
                        $path = asset('public/upload/mall_image').'/'.$value['map_image_name'];
                        $image = '<img src="'.$path.'" alt="">';
                    }
                    else
                    {
                        $image = "";
                    }
                                     
                }
                else
                {
                    $image = $value['map_image_name'];
                }
                
                $html .="<tr><td>".$image."</td><td>".$value['unique_id']."</td><td>".$value['name']."</td><td>".date("d F Y",strtotime($value['created_at']))."</td>";
                if($search == true)
                { 
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editMapImage".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this MapImage?')' href='".route($deleteroute , $value->id)."'><i class='mdi mdi-delete'></i></a></td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="6">No Records Found</td></tr>';
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