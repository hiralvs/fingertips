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

        // $rewards = Rewards::where('user_id','LIKE',"%{$search}%")
         ->orWhere('earned', 'LIKE',"%{$search}%")
        //  ->orWhere('contact', 'LIKE',"%{$search}%")
        //  ->orWhere('featured_event', 'LIKE',"%{$search}%")
         ->paginate();

        
 
        if($rewards)
         {
             $arr = array('status' => true,"data"=>$rewards[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}