<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Settings;
use Datatables;
use App\User;

class PrivacyController extends Controller
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
        $this->data['title'] = trans('Privacy Listing');
        $this->data['meta_title'] = trans('Privacy Listing');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }
        $return_data['data'] = Settings::orderBy('id', 'desc')->sortable()->paginate($perpage);
        // $return_data['category_id'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['property_user_id'] = User::select('id', 'name')->where('role', 'property_admin')->get();
        // echo "<pre>";
        // print_r($return_data['property_user_id']);
        // exit;

        //  return view('products',compact('products'));
        
        return View('admin.privacy.index', $return_data)->render();
    }
    public function addPrivacy(Request $request)
    {
        $request->request->remove('_token');
        $input = $request->all();
        $input['type'] = 'privacypolicy';

        Settings::unguard();
        $check = Settings::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Settings::find($check);
        
            $arr = array('msg' => 'Privacy Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $query = Settings::where('id', $request->id);
        $query->delete();
        return redirect()->route('privacy')->with('success', 'Privacy Deleted Successfully');
    }
    public function edit(Request $request)
    {
        $query = Settings::where('id', $request->id);
        return View('admin.privacy.edit', $query)->render();
    }
 
    public function update(Request $request)
    {
        $settings = Settings::find($request->id);
        $settings->title = $request->title;
        $settings->value =  $request->value;

        Settings::unguard();
               
        $settings->save();
       
        if (!empty($settings)) {
            $data = Settings::find($request->id);
            $arr = array('msg' => 'Privacy Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}
