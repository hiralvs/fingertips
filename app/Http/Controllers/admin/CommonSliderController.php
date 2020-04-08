<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\ShopsandMalls;
use App\Events;
use App\Attractions;
use App\Slider;
use Validator;

class CommonSliderController extends Controller
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

        if($lastsegment == 'eventslider')
        {
            $return_data['title'] = trans('Event Slider');
            $return_data['meta_title'] = trans('Event Slider');
            $return_data['data'] = Slider::select('sliders.*','event_name')->leftjoin('events', 'events.id', '=', 'sliders.common_id')->where('sliders.type','event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['events'] = Events::select('id', 'event_name')->get();
            return View("admin.eventslider.index", $return_data)->render(); 
        }
        else if($lastsegment == 'mallslider')
        {
            $return_data['title'] = trans('Mall Slider');
            $return_data['meta_title'] = trans('Mall Slider');
            $return_data['data'] = Slider::select('sliders.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'sliders.common_id')->where('sliders.type','malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['malls'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallslider.index', $return_data)->render(); 
        }
        else if($lastsegment == 'attractionslider')
        {
            $return_data['title'] = trans('Attraction Slider');
            $return_data['meta_title'] = trans('Attraction Slider');
            $return_data['data'] = Slider::select('sliders.*','attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'sliders.common_id')->where('sliders.type','attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['attraction'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionslider.index', $return_data)->render(); 
        }
    }//

        /* Function used to add  */
    public function addCommonSlider(Request $request)
    {
        $user = Auth::user();
        if($request->type == 'malls')
        {
        	$validator = Validator::make($request->all(), [
	            'mallname' => 'required',
	            'image' => 'required|image',
                'title' => 'required'
	        ]);

	        if($validator->fails()){
	            return Response()->json(['errors' => $validator->errors()]);      
	        }	
	        $common_name =  $request->mallname;
        }

        if($request->type == 'event')
        {
        	$validator = Validator::make($request->all(), [
	            'eventname' => 'required',
	            'image' => 'required|image',
                'title' => 'required'
	        ]);

	        if($validator->fails()){
	            return Response()->json(['errors' => $validator->errors()]);      
	        }	
	        $common_name =  $request->eventname;
        }

        if($request->type == 'attraction')
        {
        	$validator = Validator::make($request->all(), [
	            'attractionname' => 'required',
	            'image' => 'required|image',
                'title' => 'required'
	        ]);

	        if($validator->fails()){
	            return Response()->json(['errors' => $validator->errors()]);      
	        }	
	        $common_name =  $request->attractionname;
        }
        
        $username = "";
        if(!empty($user))
        {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $input = array(
        	'unique_id' => get_unique_id("sliders"),
        	'common_id'=>$common_name,
            'title'=>$request->title,
            'description'=>$request->description,
        	'type'=>  $request->type,
        	'created_by'=>$username ,

        );

        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/sliders/' . $filename);
            // $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['slider_image_name'] = $filename;
        }


        Slider::unguard();
        $check = Slider::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
            $arr = array('msg' => 'Slider Image Added Successfully', 'status' => true);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $lastsegment = request()->segments();
        if($lastsegment[0] == 'mallsliderdelete')
        {
            $lastsegment = 'mallslider';
        }
        if($lastsegment[0] == 'eventsliderdelete')
        {
            $lastsegment = 'eventslider';
        }
        if($lastsegment[0] == 'attractionsliderdelete')
        {
            $lastsegment = 'attractionslider';
        }


        $query = Slider::where('id',$request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'Slider Deleted Successfully');
    }
    public function update(Request $request)
    {
        $slider = Slider::find($request->id);

        if($request->type == 'malls')
        {
        	$validator = Validator::make($request->all(), [
	            'mallname' => 'required',
	        ]);
        	$common_name =  $request->mallname;
        }
        if($request->type == 'event')
        {
        	$validator = Validator::make($request->all(), [
	            'eventname' => 'required',
	        ]);
   	        $common_name =  $request->eventname;
        }
        if($request->type == 'attraction')
        {
        	$validator = Validator::make($request->all(), [
            'attractionname' => 'required',
       	 	]);
 	        $common_name =  $request->attractionname;
        }        

        if ($slider->notHavingImageInDb()){
            $rules['image'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $slider->common_id = $common_name ;
        $slider->type = $request->type;
        $slider->title = $request->title;
        $slider->description =$request->description;
          if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/sliders/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $slider->slider_image_name = $filename;
        }

        $slider->save();
       
        if (!empty($slider)) {
            $data = Slider::find($request->id);
            $arr = array('msg' => 'Slider Image Updated Successfully', 'status' => true,'data'=> $data);
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
            $sliderconnection = Slider::select('sliders.*', 'attraction_name as name')->leftjoin('attractions', 'attractions.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->orWhere('sliders.unique_id','=',"%{search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'malls')
        {
            $sliderconnection = Slider::select('sliders.*', 'shopsandmalls.name as name')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'malls');
                })->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'event')
        {
            $sliderconnection = Slider::select('sliders.*', 'event_name as name')->leftjoin('events', 'events.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'event');
                })->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%");
                })->paginate();
        }        

         if($sliderconnection)
         {
             $data = $this->htmltoexportandsearch($sliderconnection,$type,true);
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
            $query =Slider::select('sliders.*', 'event_name as name')->leftjoin('events', 'events.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'event');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%"); 
                });
            }
        } 
        elseif ($lastsegment == 'malls') 
        {
            $query = Slider::select('sliders.*', 'shopsandmalls.name as name')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'sliders.common_id')->where(function ($query) {  $query->where('sliders.type', 'malls');  });

            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                     });
            }
            
        } 
        elseif ($lastsegment == 'attraction') 
        {
           $query = Slider::select('sliders.*', 'attraction_name as name')->leftjoin('attractions', 'attractions.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'attraction');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->orWhere('sliders.unique_id','=',"%{search}%")
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
            $deleteroute = "mallslider.delete";
        }
        else if($type=='event')
        {
            $th = 'Event Name';
            $deleteroute = "eventslider.delete";
        }
        elseif ($type == 'attraction') {
             $th = 'Attraction Name';
             $deleteroute = "attractionslider.delete";
        }
        $html = "";
        if(!empty($finaldata) && $finaldata->count() > 0)
        {   
            if($search==false)
            {
                  $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                            <th>Slider Image</th>
                            <th>Slider Image id</th>
                            <th>'. $th.'</th>
                            <th>Created On</th>
                            <th>Created By</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                if($search == true)
                {
                    if($value['slider_image_name']!= null)
                    {
                        $path = asset('public/upload/sliders').'/'.$value['slider_image_name'];
                        $image = '<img src="'.$path.'" alt="">';
                    }
                    else
                    {
                        $image = "";
                    }
                                     
                }
                else
                {
                    $image = $value['slider_image_name'];
                }
                
                $html .="<tr><td>".$image."</td><td>".$value['unique_id']."</td><td>".$value['name']."</td><td>".date("d F Y",strtotime($value['created_at']))."</td><td>".$value['created_by'] ."</td>";
                if($search == true)
                { 
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editMallSlider".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Sldier?')' href='".route($deleteroute , $value->id)."'><i class='mdi mdi-delete'></i></a></td>";
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
