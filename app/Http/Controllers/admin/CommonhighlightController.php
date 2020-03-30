<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Highlights;
use App\Category;
use App\Settings;
use App\ShopsandMalls;
use DB;
use Validator;

class CommonhighlightController extends Controller
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
        $return_data['title'] = trans('Highlights');
        $return_data['meta_title'] = trans('Highlights');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='highlights.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        // $return_data['data'] = Highlights::orderBy('id', 'desc')->sortable()->paginate($perpage);
        
        $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
        $return_data['data'] = Highlights::select('highlights.*', 'shopsandmalls.id as common_id', 'name')->leftjoin('shopsandmalls', 'highlights.common_id', '=', 'shopsandmalls.id')
        ->orderBy($sort, $direction)->sortable()->paginate($perpage);

        
        // echo "<pre>";
        // print_r( $return_data['data']);
        // exit;
        return View('admin.highlights.index', $return_data)->render();
    }
    public function addHighlights(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        
        $request->request->remove('_token');
        $request->request->remove('desc');
        $input = $request->all();
        $input['type'] = 'event';
        $input['unique_id'] =  get_unique_id("Highlights");
        if ($request->hasFile('image')) {
            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/highlights/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['image'] = $filename;
        }

        Highlights::unguard();
        $check = Highlights::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Highlights::find($check);
        $arr = array('msg' => 'Highlights Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    /* Function used to delete event */
    public function delete(Request $request)
    {
        $query = Highlights::where('id', $request->id);
        $query->delete();
        return redirect()->route('highlights')->with('success', 'Highlights Deleted Successfully');
    }
    /* Function used to update event */
    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $highlights = Highlights::find($request->id);
        $highlights->title =  $request->title;
        $highlights->common_id = $request->common_id;
        $highlights->start_date =  $request->start_date;
        $highlights->start_time =  $request->start_time;
        $highlights->end_date =  $request->end_date;
        $highlights->end_time =  $request->end_time;
        $highlights->description =  $request->description;
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = 'image'.time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/highlights/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $highlights->image = $filename;
        }
        Highlights::unguard();
        $highlights->save();
       
        if (!empty($highlights)) {
            $data = Highlights::find($request->id);
            $arr = array('msg' => 'Highlights Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
     public function search(Request $request)
     {
        $search = $request->input('search');
     
        $events = Highlights::where('unique_id','LIKE',"%{$search}%")
        ->orWhere('title', 'LIKE',"%{$search}%")
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