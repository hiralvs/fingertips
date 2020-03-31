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
        	$common_name =  $request->mallsnname;
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
            $brandconnection = Slider::select('sliders.*', 'attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->orWhere('sliders.unique_id','=',"%{search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'malls')
        {
            $brandconnection = Slider::select('sliders.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'malls');
                })->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'event')
        {
            $brandconnection = Slider::select('sliders.*', 'event_name')->leftjoin('events', 'events.id', '=', 'sliders.common_id')->where(function ($query) {
                $query->where('sliders.type', 'event');
                })->where(function ($query)   use ($search){
                    $query->where('sliders.unique_id','=',"%{search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%");
                })->paginate();
        }        

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
