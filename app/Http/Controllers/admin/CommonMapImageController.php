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
                'common_id' => 'required',
            ]);

            if ($validator->fails()) {
                return Response()->json(['errors' => $validator->errors()]);
            }
            $common_name =  $request->name;
        }

        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'common_id' => 'required',
            ]);

            if ($validator->fails()) {
                return Response()->json(['errors' => $validator->errors()]);
            }
            $common_name =  $request->event_name;
        }

        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
                'common_id' => 'required',
            ]);

            if ($validator->fails()) {
                return Response()->json(['errors' => $validator->errors()]);
            }
            $common_name =  $request->attraction_name;
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        
        $common_name = 'common_id';
        $user = Auth::user();
        $username = "";
        if (!empty($user)) {
            $username = $user->name;
        }

        $request->request->remove('_token');
        $input = array(
            'unique_id' => get_unique_id("map_images"),
            'common_id'=>$request->common_id,
            'type'=>  $request->type,
        );
        
        if ($request->hasFile('map_image_name')) {
            $image = $request->File('map_image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            $path = public_path('upload/mall_image/' . $filename);
            
            Image::make($image->getRealPath())->resize(50, 50)->save($path);
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
                'common_id' => 'required',
            ]);
            $common_name =  $request->mallname;
        }
        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'common_id' => 'required',
            ]);
            $common_name =  $request->event_name;
        }
        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
            'common_id' => 'required',
            ]);
            $common_name =  $request->attractionname;
        }

        if ($mapimages->notHavingImageInDb()) {
            $rules['map_image_name'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $mapimages->common_id = $request->common_id ;
        $mapimages->type = $request->type;        
        if ($request->hasFile('map_image_name')) {
            $image = $request->File('map_image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/mall_image/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $mapimages->map_image_name = $filename;
        }
        Map_images::unguard();
        $mapimages->save();
       
        if (!empty($mapimages)) {
            $data = Map_images::find($request->id);
            $arr = array('msg' => 'Map_images Updated Successfully', 'status' => true,'data'=> $data);
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
            $map_images = Map_images::select('map_images.*', 'attractions.id as attractionid','attractions.attraction_name as attractionname','attraction_name')
            ->leftjoin('attractions', 'map_images.common_id', '=', 'attractions.id')
            // ->leftjoin('attractions', 'attractions.id', '=', 'brands_connection.common_id')
            ->where(function ($query) {
                $query->where('map_images.type', 'attraction');
                })->where(function ($query)   use ($search){
                    $query->where('attractions.attraction_name','LIKE',"%{$search}%")
                    ->orWhere('map_images.unique_id','LIKE',"%{search}%")
                    ->orWhere('common_id', 'LIKE',"%{$search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
            }        
            if($type == 'event')
            {
                $map_images = Map_images::select('map_images.*', 'events.id as eventid','events.event_name as eventname','event_name')
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
                $map_images = Map_images::select('map_images.*', 'shopsandmalls.id as mallid','shopsandmalls.name as mallname','name')
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
             $arr = array('status' => true,"data"=>$map_images[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}