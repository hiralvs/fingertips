<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            $return_data['data'] = Slider::select('sliders.*','attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'brands_connectionsliders.common_id')->where('sliders.type','attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['attraction'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionbrands.index', $return_data)->render(); 
        }
    }//

        /* Function used to add  */
    public function addCommonSlider(Request $request)
    {
        $user = Auth::user();
        $validator = $this->validate(
        $request, 
        [   
            'common_id' => 'required',
            'image'=> 'required'
        ],
        [   
            'common_id.required'    => 'Please Select Mall or Shop Name, Thank You.'
        ]
    );

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


        $slider = Slider::find($request->id);
        $slider->brand_id = $request->brand_id;
        $slider->common_id = $request->common_id;
        $slider->status = $request->status;
        $brand->save();
       
        if (!empty($brand)) {
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
            $brandconnection = Slider::select('Slider.*', 'attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'Slider.common_id')->where(function ($query) {
                $query->where('Slider.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->orWhere('Slider.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'malls')
        {
            $brandconnection = Brand_ConnectionSlider::select('Slider.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'Slider.common_id')->where(function ($query) {
                $query->where('Slider.type', 'malls');
                })->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('Slider.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'event')
        {
            $brandconnection = Slider::select('Slider.*', 'event_name')->leftjoin('events', 'events.id', '=', 'Slider.common_id')->where(function ($query) {
                $query->where('Slider.type', 'event');
                })->where(function ($query)   use ($search){
                    $query->where('brands.name','LIKE',"%{$search}%")
                    ->orWhere('Slider.unique_id','=',"%{search}%")
                    ->orWhere('brand_id', 'LIKE',"%{$search}%")
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
