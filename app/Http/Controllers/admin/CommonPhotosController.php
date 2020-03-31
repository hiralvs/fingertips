<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Photos;
use App\Settings;
use App\ShopsandMalls;
use App\Events;
use App\Attractions;
use Datatables;
use Validator;

class CommonPhotosController extends Controller
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
        $return_data['title'] = trans('Photos List');
        $return_data['meta_title'] = trans('Photos List');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='photos.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        if($lastsegment == 'eventphotos')
        {
            $return_data['title'] = trans('Event Photos');
            $return_data['meta_title'] = trans('Event Photos');
            $return_data['data'] = Photos::select('photos.*','event_name')->leftjoin('events', 'events.id', '=', 'photos.common_id')->where('photos.type','event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Events::select('id', 'event_name')->get();
            return View('admin.eventphotos.index', $return_data)->render();
        }
        else if($lastsegment == 'mallphotos')
        {
            $return_data['title'] = trans('Mall Photos');
            $return_data['meta_title'] = trans('Mall Photos');
            $return_data['data'] = Photos::select('photos.*','shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'photos.common_id')->where('photos.type','malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallphotos.index', $return_data)->render();
        }
        else if($lastsegment == 'attractionphotos')
        {
            $return_data['title'] = trans('Attraction Photos');
            $return_data['meta_title'] = trans('Attraction Photos');
            $return_data['data'] = Photos::select('photos.*','attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'photos.common_id')->where('photos.type','attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionphotos.index', $return_data)->render();
        }
    }
     /* Function used to add ema category */
    public function addPhotos(Request $request)
    {
        if($request->type == 'malls')
        {
            $validator = Validator::make($request->all(), [
                'mallname' => 'required',
                'image_name' => 'required|image',
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
                'image_name' => 'required|image',
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
                'image_name' => 'required|image',
            ]);

            if($validator->fails()){
                return Response()->json(['errors' => $validator->errors()]);      
            }   
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
            'unique_id' => get_unique_id("photos"),
            'common_id'=>$common_name,
            'type'=>  $request->type,
            'created_by'=>$username ,

        );
        
        if ($request->hasFile('image_name')) {
            $image = $request->File('image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            $path = public_path('upload/photos/' . $filename);
            
            Image::make($image->getRealPath())->save($path);
            $input['image_name'] = $filename;
        }
        Photos::unguard();
        $check = Photos::create($input)->id;
    
        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Photos::find($check);        
            $arr = array('msg' => 'Photos Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    
    /* Function used to delete event */
    public function delete(Request $request)
    {
        $lastsegment = request()->segments();
        if($lastsegment[0] == 'mallphotosdelete')
        {
            $lastsegment = 'mallphotos';
        }
        if($lastsegment[0] == 'eventphotosdelete')
        {
            $lastsegment = 'eventphotos';
        }
        if($lastsegment[0] == 'attractionphotosdelete')
        {
            $lastsegment = 'attractionphotos';
        }

        $query = Photos::where('id',$request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'Photos Deleted Successfully');
    }
    public function update(Request $request)
    {
        $photos = Photos::find($request->id);

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

        if ($photos->notHavingImageInDb()){
            $rules['image'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $photos->common_id = $common_name ;
        $photos->type = $request->type;
        if ($request->hasFile('image_name')) {

            $image = $request->File('image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/photos/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $photos->image_name = $filename;
        }
        Photos::unguard();
        $photos->save();
       
        if (!empty($photos)) {
            $data = Photos::find($request->id);
            $arr = array('msg' => 'Photos Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
}