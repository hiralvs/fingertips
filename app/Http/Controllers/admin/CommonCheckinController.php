<?php

namespace App\Http\Controllers\admin;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ShopsandMalls;
use App\Events;
use App\Attractions;
use App\Checkin;

class CommonCheckinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $lastsegment = request()->segment(count(request()->segments()));

        $auth = Auth::user();
        $return_data = array();
       
        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }
        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='id';
        }
        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        $return_data['title'] = trans('Check In');
        $return_data['meta_title'] = trans('Check In');

        if($lastsegment == 'eventcheckin')
        {   
        	$return_data['redirect'] =  $lastsegment;        
            $return_data['data'] = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where('checkin.esma_type','event')->whereNotNull('checkout_time')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            return View("admin.checkin.index", $return_data)->render(); 
        }
        else if($lastsegment == 'mallcheckin')
        {
        	$return_data['redirect'] =  $lastsegment;
            $return_data['data'] = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where('checkin.esma_type','malls')->whereNotNull('checkout_time')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            return View('admin.checkin.index', $return_data)->render(); 
        }
        else if($lastsegment == 'attractioncheckin')
        {
        	$return_data['redirect'] =  $lastsegment;
			$return_data['data'] = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where('checkin.esma_type','attraction')->whereNotNull('checkout_time')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            return View('admin.checkin.index', $return_data)->render(); 
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        if($type == 'mallcheckin')
        {
            $checkin = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','malls')->whereNotNull('checkout_time');
                })->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%");
                })->paginate();
        }
        if($type == 'eventcheckin')
        {
            $checkin = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','event')->whereNotNull('checkout_time');
                })->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%");
                })->paginate();
        }
        if($type == 'attractioncheckin')
        {
            $checkin = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','attraction')->whereNotNull('checkout_time');
                })->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%");
                })->paginate();
        }        

         if($checkin)
         {
            $data = $this->htmltoexportandsearch($checkin,true);
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
        if ($lastsegment == 'eventcheckin') 
        {
            $query = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','event')->whereNotNull('checkout_time');
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%"); 
                });
            }
        } 
        elseif ($lastsegment == 'mallcheckin') 
        {
            $query = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','malls')->whereNotNull('checkout_time');;
                });

            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%");
                     });
            }
            
        } 
        elseif ($lastsegment == 'attractioncheckin') 
        {
           $query = Checkin::select('checkin.*','users.name as username')->leftjoin('users', 'users.id', '=', 'checkin.user_id')->where(function ($query) {
                $query->where('checkin.esma_type','attraction')->whereNotNull('checkout_time');;
                });
            if($request->search != "")
            {
                $query = $query->where(function ($query)   use ($search){
                    $query->where('users.name','LIKE',"%{$search}%")
                    ->orWhere('users.id','=',"%{search}%");
                     }); 
            }
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
                            <th>Username</th>
                            <th>Check In Time</th>
                            <th>Check Out Time</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                $id = $key+1;
                $html .="<tr><td>".$id."</td><td>".$value['username'] ."</td><td>".$value['checkin_time']."</td><td>".$value['checkout_time']."</td>";
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="4">No Records Found</td></tr>';
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
