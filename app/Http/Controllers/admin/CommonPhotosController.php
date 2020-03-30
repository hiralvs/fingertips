<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Photos;
use App\Settings;
use App\ShopsandMalls;
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
        $return_data['data'] = Photos::select('photos.*','shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'photos.common_id')->where('photos.type','malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
        $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
        // echo "<pre>";
        // print_r( $return_data['data']);
        // exit;
        return View('admin.photos.index', $return_data)->render();
    }
     /* Function used to add ema category */
    public function addPhotos(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'image_name' => 'required|image',
            'common_id' => 'required',
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
        $input = $request->all();
        $input['type'] = 'malls';
        $input['unique_id'] =  get_unique_id("photos");
        $input['created_by'] =  $username ;
         
        if ($request->hasFile('image_name')) {
            $image = $request->File('image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            $path = public_path('upload/common_photos/' . $filename);
            
            Image::make($image->getRealPath())->resize(50, 50)->save($path);
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
        $query = Photos::where('id',$request->id);
        $query->delete();
        return redirect()->route('photos')->with('success', 'Photos Deleted Successfully');
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'common_id' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }


        $photos = Photos::find($request->id);

        $photos->common_id = $request->common_id;
        if ($request->hasFile('image_name')) {

            $image = $request->File('image_name');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/common_photos/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
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