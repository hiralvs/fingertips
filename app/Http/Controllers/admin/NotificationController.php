<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Notification;
use Datatables;
use App\Category;
use App\User;
use Validator;

class NotificationController extends Controller
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

    /* Function used to display Notification */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Notification Listing');
        $return_data['meta_title'] = trans('Notification Listing');

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
            $sort='notifications.id';
        }

        if($request->direction)
        {
            $direction=$request->direction;
        }
        else
        {
            $direction='desc';
        }
        $return_data['data'] = Notification::orderBy($sort, $direction)->sortable()->paginate($perpage);

        return View('admin.notification.index', $return_data)->render();

    }
     /* Function used to add ema category */
    public function addNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $user = Auth::user();
        $request->request->remove('_token');
        $input = $request->all();
        $username = "";
        if (!empty($user)) {
            $username = $user->name;
        }
        $input['created_by'] =  $username ;

        Notification::unguard();
        $check = Notification::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Notification::find($check);
        
        $arr = array('msg' => 'Notification Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    /* Function used to delete notification */
    public function delete(Request $request)
    {
        $query = Notification::where('id',$request->id);
        $query->delete();
        return redirect()->route('notifications')->with('success', 'Notification Deleted Successfully');
    }

    /* Function used to update notification */
    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $notification = Notification::find($request->id);
        
        $notification->title =  $request->title;
        $notification->description =  $request->description;
        $notification->type =  $request->type;
        $notification->url =  $request->url;
        $notification->ema =  $request->ema;
        $notification->inboundtext =  $request->inboundtext;
        Notification::unguard();
        $notification->save();
       
        if (!empty($notification)) {
            $data = Notification::find($request->id);
            $arr = array('msg' => 'Notification Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
    /* Function user to search attraction data */
    public function search(Request $request)
     {
        $search = $request->input('search');
     
        $notification = Notification::where('title','LIKE',"%{$search}%")
         ->orWhere('description', 'LIKE',"%{$search}%")
         ->orWhere('created_by', 'LIKE',"%{$search}%")
         ->orWhere('created_at', 'LIKE',"%{$search}%")
         ->paginate();
 
        if($notification)
         {
             $arr = array('status' => true,"data"=>$notification[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}