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
        $return_data['category'] = Category::select('id', 'category_name')->where('type','event')->orderBy('category_name','asc')->get();
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
            $data = $this->htmltoexportandsearch($events,true);
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
        $query = Events::select('events.*','users.id as userid','users.name as propertyadmin')->leftjoin('users', 'events.property_admin_user_id', '=', 'users.id');

        if($request->search != "")
        {
            $query = $query->where('event_name','LIKE',"%{$search}%")
                 ->orWhere('events.unique_id', 'LIKE',"%{$search}%")
                 ->orWhere('location', 'LIKE',"%{$search}%")
                 ->orWhere('event_start_date', 'LIKE',"%{$search}%")
                 ->orWhere('start_time', 'LIKE',"%{$search}%")
                 ->orWhere('description', 'LIKE',"%{$search}%")
                 ->orWhere('events.created_at', 'LIKE',"%{$search}%");
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Opening Date</th>
                            <th>Starting Time</th>
                            <th>Description</th>
                            <th>Created on</th>
                            <th>Created By</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                if($search == true)
                {
                    if($value['event_image']!= null)
                    {
                        $path = asset('public/upload/events').'/'.$value['event_image'];
                        $image = '<img src="'.$path.'" alt="">';
                    }
                    else
                    {
                        $image = "";
                    }
                                     
                }
                else
                {
                    $image = $value['event_image'];
                }
                
                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr><td>".$value['unique_id']."</td><td>".$image."</td><td>".$value['event_name'] ."</td><td>".$value['location']."</td><td>".$value['event_start_date']."</td><td>".$value['start_time']."</td><td>".$value['description']."</td><td>".$cdate."</td><td>".$value['created_by']."</td>";
                if($search == true)
                {
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editEvents".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Events?')' href='".route('events.delete', $value->id)."'><i class='mdi mdi-delete'></i></a> 
                          </td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="11">No Records Found</td></tr>';
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

    public function updatebanner(Request $request)
    {
        $val = $request->chk;
        $id = $request->id;
        $events = Events::find($id);
        $events->set_as_banner = $val;
        Events::unguard();
        $affectedrow =  $events->save();

        if($affectedrow)
        {
            $arr = array('status' => true,"msg"=>'Event is updated for banner');    
        }
        else{
         $arr = array('status' => false,"msg"=>"Event is not updated for banner","data"=>[]);    
        }

        return Response()->json($arr);
    }
}