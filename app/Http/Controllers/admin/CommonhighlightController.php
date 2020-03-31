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

        if ($lastsegment == 'eventhighlights') 
        {
            $return_data['title'] = trans('Event Highlights');
            $return_data['meta_title'] = trans('Event Highlights');
            $return_data['data'] = Highlights::select('highlights.*', 'event_name')->leftjoin('events', 'events.id', '=', 'highlights.common_id')->where('highlights.type', 'event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = Events::select('id', 'event_name')->get();
            return View('admin.eventhighlights.index', $return_data)->render();
        } 
        elseif ($lastsegment == 'mallhighlights') 
        {
            $return_data['title'] = trans('Mall Highlights');
            $return_data['meta_title'] = trans('Mall Highlights');
            $return_data['data'] = Highlights::select('highlights.*', 'shopsandmalls.name as mallname')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'highlights.common_id')->where('highlights.type', 'malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallhighlights.index', $return_data)->render();
        } 
        elseif ($lastsegment == 'attractionhighlights') 
        {
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
                'title' => 'required',
            ]);

            $common_name =  $request->mallname;
        }

        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'eventname' => 'required',
                'title' => 'required',
            ]);

            $common_name =  $request->eventname;
        }

        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
                'attractionname' => 'required',
                'title' => 'required',
            ]);

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
            'unique_id' => get_unique_id("highlights"),
            'common_id'=>$common_name,
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
    
        $arr = array('msg' => 'Something goes to wrong. Please try again later', 'status' => false);
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
                'title' => 'required',
            ]);

            $common_name =  $request->mallname;
        }

        if ($request->type == 'event') {
            $validator = Validator::make($request->all(), [
                'eventname' => 'required',
                'title' => 'required',
            ]);

            $common_name =  $request->eventname;
        }

        if ($request->type == 'attraction') {
            $validator = Validator::make($request->all(), [
                'attractionname' => 'required',
                'title' => 'required',
            ]);

            $common_name =  $request->attractionname;
        }

        if ($highlights->notHavingImageInDb()) {
            $rules['image'] = 'required|image';
        }

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $highlights->common_id = $common_name ;
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

    public function search(Request $request)
    {
        $search = $request->input('search');
        $type = $request->type;;
        if($type == 'attraction')
        {
            $highlights = Highlights::select('highlights.*', 'attraction_name as name')->leftjoin('attractions', 'attractions.id', '=', 'highlights.common_id')->where(function ($query) {
                $query->where('highlights.type', 'attraction');
                })->where(function ($query)   use ($search){
                     $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'malls')
        {
            $highlights = Highlights::select('highlights.*', 'shopsandmalls.name as name')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'highlights.common_id')->where(function ($query) {
                $query->where('highlights.type', 'malls');
                })->where(function ($query)   use ($search){
                    $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
                })->paginate();
        }
        if($type == 'event')
        {
            $highlights = Highlights::select('highlights.*', 'event_name as name')->leftjoin('events', 'events.id', '=', 'highlights.common_id')->where(function ($query) {
                $query->where('highlights.type', 'event');
                })->where(function ($query)   use ($search){
                    $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%");
                })->paginate();
        }        

         if($highlights)
         {
            $data = $this->htmltoexportandsearch($highlights,$type,true);
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
        $lastsegment = $request->type;
        if ($lastsegment == 'event') 
        {
            $query = Highlights::select('highlights.*', 'event_name as name')->leftjoin('events', 'events.id', '=', 'highlights.common_id')->where('highlights.type', 'event');
            if($request->search != "")
            {
                $query = $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('event_name', 'LIKE',"%{$search}%");
            }
        } 
        elseif ($lastsegment == 'malls') 
        {
            $query = Highlights::select('highlights.*', 'shopsandmalls.name as name')->leftjoin('shopsandmalls', 'shopsandmalls.id', '=', 'highlights.common_id')->where('highlights.type', 'malls');

            if($request->search != "")
            {
                $query = $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('shopsandmalls.name', 'LIKE',"%{$search}%");
            }
            
        } 
        elseif ($lastsegment == 'attraction') 
        {
           $query = Highlights::select('highlights.*', 'attraction_name as name')->leftjoin('attractions', 'attractions.id', '=', 'highlights.common_id')->where('highlights.type', 'attraction');
            if($request->search != "")
            {
                $query = $query->where('highlights.unique_id','=',"%{search}%")
                    ->orWhere('highlights.title', 'LIKE',"%{$search}%")
                    ->orWhere('attraction_name', 'LIKE',"%{$search}%");
            }
        }

        $finaldata = $query->get();
        $this->htmltoexportandsearch($finaldata,$lastsegment);
       
    }

    public function htmltoexportandsearch($finaldata,$type,$search=false)
    {
        if($type == 'malls')
        {
            $th = 'Shops and Malls Name';
            $deleteroute = "mallhighlights.delete";
        }
        else if($type=='event')
        {
            $th = 'Event Name';
            $deleteroute = "eventhighlights.delete";
        }
        elseif ($type == 'attraction') {
             $th = 'Attraction Name';
             $deleteroute = "attractionhighlights.delete";
        }
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
                            <th>Title</th>
                            <th>'. $th.'</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                if($search == true)
                {
                    if($value['image']!= null)
                    {
                        $path = asset('public/upload/highlights').'/'.$value['image'];
                        $image = '<img src="'.$path.'" alt="">';
                    }
                    else
                    {
                        $image = "";
                    }
                                     
                }
                else
                {
                    $image = $value['image'];
                }
                
                $html .="<tr><td>".$value['unique_id']."</td><td>".$image ."</td><td>".$value['title']."</td><td>".$value['name']."</td>";
                if($search == true)
                {                    
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editHighlights".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Highlights?')' href='".route($deleteroute , $value->id)."'><i class='mdi mdi-delete'></i></a></td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="5">No Records Found</td></tr>';
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

}
