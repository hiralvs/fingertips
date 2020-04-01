<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Directory;
use App\Category;
use App\Settings;
use DB;
use Validator;

class DirectoryController extends Controller
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
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Directory Listing');
        $return_data['meta_title'] = trans('Directory Listing');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='directory.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        $return_data['data'] = Directory::select('directory.*', 'category.id as category_id', 'settings.value as floorname', 'category_name')->leftjoin('category', 'directory.category_id', '=', 'category.id')

        ->leftjoin('settings', 'settings.id', '=', 'directory.floor')
        ->orderBy($sort, $direction)->sortable()->paginate($perpage);
        $return_data['category_name'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['floor'] = Settings::where('type', 'floor')->orderBy('id', 'asc')->get();

        $diretory = Directory::select('directory.*', 'category.category_name as category_id', 'settings.value as floorname', 'category_name')
        ->leftjoin('category', 'directory.category_id', '=', 'category.id')
        ->leftjoin('settings', 'settings.floor', '=', 'directory.floor');

        // echo "<pre>";
        // print_r( $return_data['data']);
        // exit;
        return View('admin.directory.index', $return_data)->render();
    }
    public function addDirectory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'floor' => 'required',
            'unit_number' => 'required',
        ]);


        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }



        $user = Auth::user();
        // $username = "";
        // if (!empty($user)) {
        //     $username = $user->name;
        // }
        $request->request->remove('_token');
        $request->request->remove('desc');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id("directory");
        // $input['category_id'] =  implode(",", $input['category_id']);
        // $input['created_by'] =  $username ;
        Directory::unguard();
        $check = Directory::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Directory::find($check);
            // $data['propertyadmin'] = User::select('name as propertyadmin')->find($data['events']->property_admin_user_id);
        
            $arr = array('msg' => 'Directory Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    /* Function used to delete event */
    public function delete(Request $request)
    {
        $query = Directory::where('id', $request->id);
        $query->delete();
        return redirect()->route('directory')->with('success', 'Directory Deleted Successfully');
    }
    /* Function used to update event */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'floor' => 'required',
            'unit_number' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $events = Directory::find($request->id);
        $events->name =  $request->name;
        $events->category_id = $request->category_id;
        ;
        $events->floor = $request->floor;
        $events->unit_number = $request->unit_number;
        $events->contact = $request->contact;
        $events->openinghrs =  $request->openinghrs;
        $events->closinghrs =  $request->closinghrs;
        $events->description =  $request->description;

        // Directory::unguard();
        $events->save();
       
        if (!empty($events)) {
            $data = Directory::find($request->id);
            $arr = array('msg' => 'Directory Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');
     
            $diretory = Directory::select('directory.*', 'category.category_name as category_id', 'settings.value as floorname', 'category_name')
            ->leftjoin('category', 'directory.category_id', '=', 'category.id')
            ->leftjoin('settings', 'settings.id', '=', 'directory.floor')
            ->where('name','LIKE',"%{$search}%")
            ->orWhere('unique_id', 'LIKE',"%{$search}%")
            ->orWhere('openinghrs', 'LIKE',"%{$search}%")
            ->orWhere('description', 'LIKE',"%{$search}%")
            ->paginate(); 
        if ($diretory) {
            $arr = array('status' => true,"data"=>$diretory[0]);
        } else {
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);
        }
 
        return Response()->json($arr);
    }
}
