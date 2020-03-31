<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Highlights;
use App\Attractions;
use App\Category;
use App\Events;
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
        $lastsegment = request()->segment(count(request()->segments()));

        $auth = Auth::user();
        // $return_data = array();
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

        if ($lastsegment == 'eventhighlights') {
            $return_data['title'] = trans('Event Highlights');
            $return_data['meta_title'] = trans('Event Highlights');
            $return_data['data'] = Highlights::select('highlights.*', 'event_name')->leftjoin('events', 'events.id', '=', 'highlights.common_id')->where('highlights.type', 'event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Events::select('id', 'event_name')->get();
            return View('admin.eventhighlights.index', $return_data)->render();
        } elseif ($lastsegment == 'mallhighlights') {
            $return_data['title'] = trans('Mall Highlights');
            $return_data['meta_title'] = trans('Mall Highlights');
            $return_data['data'] = Highlights::select('highlights.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'highlights.common_id')->where('highlights.type', 'malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallhighlights.index', $return_data)->render();
        } elseif ($lastsegment == 'attractionhighlights') {
            $return_data['title'] = trans('Attraction Highlights');
            $return_data['meta_title'] = trans('Attraction Highlights');
            $return_data['data'] = Highlights::select('highlights.*', 'attraction_name')->leftjoin('attractions', 'attractions.id', '=', 'highlights.common_id')->where('highlights.type', 'attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Attractions::select('id', 'attraction_name')->get();
            return View('admin.attractionhighlights.index', $return_data)->render();
        }
    }
    

    public function addHighlights(Request $request)
    {
        if ($request->type == 'malls') {
            $validator = Validator::make($request->all(), [
                'mallname' => 'required',
                'image_name' => 'required|image',
            ]);

            if ($validator->fails()) {
                return Response()->json(['errors' => $validator->errors()]);
            }
            $common_name =  $request->mallname;
        }

        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'common_id' => 'required',
            ]);

            if ($validator->fails()) {
                return Response()->json(['errors' => $validator->errors()]);
            }
            $common_name =  $request->event_name;
        }

        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
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
            'unique_id' => get_unique_id("Highlights"),
            'common_id'=>$request->common_id,
            'type'=>  $request->type,
            'title'=>  $request->title,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'description'=>$request->description,
        );
        
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
        if ($check) {
            $data = Highlights::find($check);
            $arr = array('msg' => 'Highlights Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    public function delete(Request $request)
    {
        $lastsegment = request()->segments();
        if ($lastsegment[0] == 'mallhighlightsdelete') {
            $lastsegment = 'mallhighlights';
        }
        if ($lastsegment[0] == 'eventhighlightsdelete') {
            $lastsegment = 'eventhighlights';
        }
        if ($lastsegment[0] == 'attractionhighlightsdelete') {
            $lastsegment = 'attractionhighlights';
        }

        $query = Highlights::where('id', $request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'Highlights Deleted Successfully');
    }

    public function update(Request $request)
    {
        $highlights = Highlights::find($request->id);

        if ($request->type == 'malls') {
            $validator = Validator::make($request->all(), [
                'mallname' => 'required',
            ]);
            $common_name =  $request->mallname;
        }
        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                // 'common_id' => 'required',
            ]);
            $common_name =  $request->event_name;
        }
        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
            'attractionname' => 'required',
            ]);
            $common_name =  $request->attractionname;
        }

        if ($highlights->notHavingImageInDb()) {
            $rules['image'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $highlights->common_id = $request->common_id ;
        $highlights->type = $request->type;
        $highlights->title = $request->title;
        $highlights->start_date = $request->start_date;
        $highlights->end_date = $request->end_date;
        $highlights->start_time = $request->start_time;
        $highlights->end_time = $request->end_time;
        $highlights->description = $request->description;
        
        if ($request->hasFile('image')) {
            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

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
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
}
