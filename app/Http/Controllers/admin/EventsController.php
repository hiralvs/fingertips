<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Events;
use Datatables;
use App\Category;
use App\Area;
use App\User;
use Validator;


class EventsController extends Controller
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

    /* Function used to display events */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Events Listing');
        $return_data['meta_title'] = trans('Events Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }

        if($request->sort)
        {
            $sort=$request->sort;
        }
        else
        {
            $sort='events.id';
        }

        if($request->direction)
        {
            $direction=$request->direction;
        }
        else
        {
            $direction='desc';
        }

        $return_data['data'] = Events::select('events.*','users.id as userid','users.name as propertyadmin')->leftjoin('users', 'events.property_admin_user_id', '=', 'users.id')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        
        $return_data['property_admin'] = User::select('id', 'name')->where('role','property_admin')->get();
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name','asc')->get();
        $return_data['area'] = Area::select('id', 'area_name')->orderBy('area_name','asc')->get();
        // $return_data['property_user_id'] = User::select('id', 'name')->where('role', 'property_admin')->get();

        // $return_data['data'] = ShopsandMalls::select('shopsandmalls.*', 'users.id as userid', 'users.name as propertyadmin')->leftjoin('users', 'shopsandmalls.property_admin_user_id', '=', 'users.id')->orderBy($sort, $direction)->sortable()->paginate($perpage);
        
        // $return_data['property_admin'] = User::select('id', 'name')->where('role', 'property_admin')->get();
        // $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        // $return_data['area'] = Area::select('id', 'area_name')->orderBy('area_name', 'asc')->get();

        
        return View('admin.events.index',$return_data)->render();
    }
     public function addEvents(Request $request) {
            $validator = Validator::make($request->all(), [
            'event_image' => 'required|image',
            'event_name' => 'required|max:255',
            'event_start_date' => 'required',
            'start_time' => 'required',
            'event_end_date' => 'required|after:event_start_date',
            'end_time' => 'required',
            'contact' => 'required|numeric',
            'property_admin_user_id' => 'required',
            'category_id' => 'required',
            'area_id' => 'required',
            'featured_event' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }


        $user = Auth::user();
        $username = "";
        if (!empty($user)) {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $request->request->remove('lat');
        $request->request->remove('long');
        $request->request->remove('desc');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id("events");
        $input['category_id'] =  implode(",", $input['category_id']);
        $input['created_by'] =  $username ;
        if ($request->hasFile('event_image')) {
            $image = $request->File('event_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/events/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['event_image'] = $filename;
        }
        Events::unguard();
        $check = Events::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Events::find($check);
            // $data['propertyadmin'] = User::select('name as propertyadmin')->find($data['events']->property_admin_user_id);
        
            $arr = array('msg' => 'Events Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);

     }
    
    /* Function used to delete event */
    public function delete(Request $request)
    {
        $query = Events::where('id',$request->id);
        $query->delete();
        return redirect()->route('event')->with('success', 'Event Deleted Successfully');
    }

    /* Function used to update event */
    public function update(Request $request)
    {
        $events = Events::find($request->id);

        $validator = Validator::make($request->all(), [
            'event_name' => 'required|max:255',
            'event_start_date' => 'required',
            'start_time' => 'required',
            'event_end_date' => 'required',
            'end_time' => 'required',
            'contact' => 'required',
            'property_admin_user_id' => 'required',
            'category_id' => 'required',
            'area_id' => 'required',
            'featured_event' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        if ($events->notHavingImageInDb()){
            $rules['event_image'] = 'required|image';
        }

        $categoryid = implode(",",$request->category_id);
        $events->event_name =  $request->event_name;
        $events->location =  $request->location;
        $events->latitude =  $request->latitude;
        $events->longitude =  $request->longitude;
        $events->event_start_date =  $request->event_start_date;
        $events->event_end_date =  $request->event_end_date;
        $events->start_time =  $request->start_time;
        $events->end_time =  $request->end_time;
        $events->contact =  $request->contact;
        $events->property_admin_user_id =  $request->property_admin_user_id;
        $events->category_id =  $categoryid;
        $events->area_id =  $request->area_id;
        $events->featured_event =  $request->featured_event;
        $events->description =  $request->description;
        if ($request->hasFile('event_image')) {

            $image = $request->File('event_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/events/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $events->event_image = $filename;
        }
        Events::unguard();
        $events->save();
       
        if (!empty($events)) {
            $data = Events::find($request->id);
            $arr = array('msg' => 'events Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
    /* Function user to search user data */
     public function search(Request $request)
     {
        $search = $request->input('search');
     
        $events = Events::where('event_name','LIKE',"%{$search}%")
         ->orWhere('unique_id', 'LIKE',"%{$search}%")
         ->orWhere('location', 'LIKE',"%{$search}%")
         ->orWhere('event_start_date', 'LIKE',"%{$search}%")
         ->orWhere('start_time', 'LIKE',"%{$search}%")
         ->orWhere('description', 'LIKE',"%{$search}%")
         ->orWhere('created_at', 'LIKE',"%{$search}%")
        //  ->orWhere('contact', 'LIKE',"%{$search}%")
        //  ->orWhere('featured_event', 'LIKE',"%{$search}%")
         ->paginate();

        
 
        if($events)
         {
             $arr = array('status' => true,"data"=>$events[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}