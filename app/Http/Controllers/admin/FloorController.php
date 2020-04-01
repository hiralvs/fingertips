<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Settings;
use Datatables;
use App\User;

class FloorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Floor');
        $return_data['meta_title'] = trans('Floor');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }
        $return_data['data'] = Settings::where('type', 'floor')->orderBy('id', 'desc')->sortable()->paginate($perpage);
        return View('admin.floor.index', $return_data)->render();
    }
    public function addFloor(Request $request)
    {
        $request->request->remove('_token');
        
        $input = $request->all();
        $input['type'] = 'floor';

        Settings::unguard();
        $check = Settings::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Settings::find($check);
        
        $arr = array('msg' => 'Floor Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $query = Settings::where('id', $request->id);
        $query->delete();
        return redirect()->route('floor')->with('success', 'Floor Deleted Successfully');
    }
    public function update(Request $request)
    {        
        $settings = Settings::find($request->id);
        $settings->value =  $request->floor;

        Settings::unguard();
               
        $settings->save();
       
        if (!empty($settings)) {
            $data = Settings::find($request->id);
            $arr = array('msg' => 'Floor Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}