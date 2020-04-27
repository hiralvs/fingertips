<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Brand;
use App\Checkin;
use DB;
use App\Charts\UserChart;
use App\Favorites;
use App\Order;
use App\ShopsandMalls;
use App\Attractions;
use App\Events;

class DashboardController extends Controller
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

    /* Function user to display user list */
    public function index(Request $request) 
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Dashboard');
        $return_data['meta_title'] = trans('Dashboard');
        //$reserve = Reserve::all()->groupBy('day')->count();
        $return_data['admincount'] = User::where('role','customer')->count();
        $return_data['brandcount'] = Brand::all()->count();
        $return_data['checkincount'] = Checkin::all()->count();
        $return_data['orderscount'] = DB::table('order_details')->count();
        $return_data['nearmecount'] = DB::table('settings')->where('type','nearme')->get();

        $return_data['toppurchases'] = Order::select("orders.order_id",'products.name as product_name','orders.amount','orders.created_at','order_details.status')->leftjoin('products','products.id','=','orders.product_id')->join('order_details','order_details.order_id','=','orders.order_id')->orderby('created_at','desc')->limit(20)->get();
        $return_data['topplacesvisiter'] = Checkin::Select('property_name','esma_type','user_id','checkin.created_at','users.name as username')->join('users','users.id','=','checkin.user_id')->orderby('created_at','desc')->limit(20)->get();
        $return_data['topfavourites'] =Favorites::select('favorites.id','event_name','attraction_name','name','favorites.created_at')->leftjoin('events', function ($join) { $join->on('events.id', '=', 'favorites.common_id')->where('favorites.type', '=', 'event');})->leftjoin('attractions', function ($join) { $join->on('attractions.id', '=', 'favorites.common_id')->where('favorites.type', '=', 'attraction');})->leftjoin('shopsandmalls', function ($join) { $join->on('shopsandmalls.id', '=', 'favorites.common_id')->where('favorites.type', '=', 'malls');})->orderby('favorites.created_at','desc')->limit(20)->get();
        $return_data['topsocialshare'] = DB::table('social_media_share')->orderby('id','desc')->limit(20)->get();
        
        // $return_data['customerdata'] = User::select(DB::raw('count(id) as count'),DB::raw("MONTHNAME(created_at) as monthlab"))->where('role','customer')->groupby(DB::raw("MONTHNAME(created_at)"))->get()->pluck('count','monthlab');
        
        // $usersChart = new UserChart;
        $return_data['dob'] = DB::table('users')->select('dob as age')->orderby('dob')->get();
        foreach ($return_data['dob']  as $key => $value) {
            $dob[] = date('Y',strtotime($value->age));
        }
        $return_data['dob'] = array_unique($dob);
        // if(!empty($return_data['customerdata']) && $return_data['customerdata']->count() > 0)
        // {
        //     foreach($return_data['customerdata'] as $m=>$val)
        //     {
        //         $label[] = $m;
        //         $labelval[] = $val;
        //     }
        //     $usersChart->labels($label);
        //     $borderColors = [
        //         "rgba(255, 99, 132, 1.0)",
        //         "rgba(22,160,133, 1.0)",
        //         "rgba(255, 205, 86, 1.0)",
        //         "rgba(51,105,232, 1.0)",
        //         "rgba(244,67,54, 1.0)",
        //         "rgba(34,198,246, 1.0)",
        //         "rgba(153, 102, 255, 1.0)",
        //         "rgba(255, 159, 64, 1.0)",
        //         "rgba(233,30,99, 1.0)",
        //         "rgba(205,220,57, 1.0)"
        //     ];
        //     $fillColors = [
        //         "rgba(255, 99, 132, 0.2)",
        //         "rgba(22,160,133, 0.2)",
        //         "rgba(255, 205, 86, 0.2)",
        //         "rgba(51,105,232, 0.2)",
        //         "rgba(244,67,54, 0.2)",
        //         "rgba(34,198,246, 0.2)",
        //         "rgba(153, 102, 255, 0.2)",
        //         "rgba(255, 159, 64, 0.2)",
        //         "rgba(233,30,99, 0.2)",
        //         "rgba(205,220,57, 0.2)"

        //     ];
        
        //     $usersChart->dataset('Customer Signup Chart', 'bar', $labelval)
        //     ->color($borderColors)
        //     ->backgroundcolor($fillColors);
        // }
        // else
        // {
        //     $label[] = "";
        //     $labelval[] = "No Data Found";
        //     $usersChart->labels($label);
        //     $usersChart->dataset('Customer Signup Chart', 'bar', $labelval);
        // }
        
        return view('admin.dashboard.index', [ "return_data"=>$return_data]);
    }

    public function customerchart(Request $request)
    {
        $gender = $request->gender;
        $dob = $request->dob;
        $query = User::select(DB::raw('count(id) as count'),DB::raw("MONTHNAME(created_at) as monthlab"))->where('role','customer')->groupby(DB::raw("MONTHNAME(created_at)"));

        if( $gender != "")
        {
            $query = $query->where('gender',$gender);
        }
         if( $dob != "")
        {
            $query = $query->whereYear('dob',$dob);
        }

        $result = $query->get();
        return response()->json($result);
    }

    public function shopchart(Request $request)
    {
        $query = Checkin::select(DB::raw('count(checkin.id) as count'),'shopsandmalls.location')->join('shopsandmalls', function ($join) { $join->on('shopsandmalls.id', '=', 'checkin.esma_id')->where('shopsandmalls.type', '=', 'shop');})->where('esma_type','malls')->groupby('location');

        $result = $query->get();
        return response()->json($result);
    }

    public function mallchart(Request $request)
    {
        $query = Checkin::select(DB::raw('count(checkin.id) as count'),'shopsandmalls.location')->join('shopsandmalls', function ($join) { $join->on('shopsandmalls.id', '=', 'checkin.esma_id')->where('shopsandmalls.type', '=', 'mall');})->where('esma_type','malls')->groupby('location');

        $result = $query->get();
        return response()->json($result);
    }

    public function eventchart(Request $request)
    {
        $query = Checkin::select(DB::raw('count(checkin.id) as count'),'events.location')->join('events','events.id', '=', 'checkin.esma_id')->where('esma_type', '=', 'event')->groupby('location');

        $result = $query->get();
        return response()->json($result);
    }

    public function attractionchart(Request $request)
    {
        $query = Checkin::select(DB::raw('count(checkin.id) as count'),'attractions.location')->join('attractions','attractions.id', '=', 'checkin.esma_id')->where('esma_type', '=', 'attraction')->where('esma_type','attraction')->groupby('location');

        $result = $query->get();
        return response()->json($result);
    }

    public function purchasechart(Request $request)
    {

        $query = Order::select(DB::raw('sum(amount) as totalamt'),DB::raw("MONTHNAME(created_at) as monthlab"))->groupby('monthlab')->orderby('totalamt','desc');

        $result = $query->get();
        return response()->json($result);
    }
    
}
