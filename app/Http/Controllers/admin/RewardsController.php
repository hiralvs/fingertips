<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Rewards;
use Datatables;
use App\User;

class RewardsController extends Controller
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
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Rewards Listing');
        $return_data['meta_title'] = trans('Rewards Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }
        $return_data['data'] = Rewards::select('users.id', 'users.name', 'users.created_at', 'rewards.*')->leftjoin('users','users.id','=','rewards.user_id')->orderBy('users.id','desc')->sortable()->paginate($perpage);
                
        return View('admin.rewards.index', $return_data)->render();
    }
    public function delete(Request $request){
        $query = Rewards::where('id',$request->id);
        $query->delete();
        return redirect()->route('rewards')->with('success', 'Rewards Deleted Successfully');
    }
    public function search(Request $request)
     {
        $search = $request->input('search');
        $rewards = Rewards::select('users.id', 'users.name', 'users.created_at', 'rewards.*')->leftjoin('users', 'users.id', '=', 'rewards.user_id')->where('users.name','LIKE',"%{$search}%")

         ->orWhere('earned', 'LIKE',"%{$search}%")
         ->paginate();

        if($rewards)
         {  
            $data = $this->htmltoexportandsearch($rewards,true);
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
        $query = Rewards::select('users.id', 'users.name', 'users.created_at', 'rewards.*')->leftjoin('users', 'users.id', '=', 'rewards.user_id');

        if($request->search != "")
        {
            $query = $query->where('users.name','LIKE',"%{$search}%")
         ->orWhere('earned', 'LIKE',"%{$search}%");
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
                            <th>Customer Name</th>
                            <th>Wallet Value</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                $id = $key+1;      
                $html .="<tr><td>".$value['name']."</td><td>".$value['earned']."</td>";
                if($search == true)
                {
                    $html .="<td> <a class='delete' onclick='return confirm('Are you sure you want to delete this Reward?')' href='".route('rewards.delete', $value->id)."'><i class='mdi mdi-delete'></i></a>
                          </td>";
                }
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