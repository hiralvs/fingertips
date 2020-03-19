<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Brand;
use DB;
use App\Charts\UserChart;

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
        $return_data['customerdata'] = User::select(DB::raw('count(id) as count'),DB::raw("MONTHNAME(created_at) as monthlab"))->where('role','customer')->groupby(DB::raw("MONTHNAME(created_at)"))->get()->pluck('count','monthlab');
        
        foreach($return_data['customerdata'] as $m=>$val)
        {
            $label[] = $m;
            $labelval[] = $val;
        }
        $usersChart = new UserChart;
        $usersChart->labels($label);
        $borderColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)",
            "rgba(244,67,54, 1.0)",
            "rgba(34,198,246, 1.0)",
            "rgba(153, 102, 255, 1.0)",
            "rgba(255, 159, 64, 1.0)",
            "rgba(233,30,99, 1.0)",
            "rgba(205,220,57, 1.0)"
        ];
        $fillColors = [
            "rgba(255, 99, 132, 0.2)",
            "rgba(22,160,133, 0.2)",
            "rgba(255, 205, 86, 0.2)",
            "rgba(51,105,232, 0.2)",
            "rgba(244,67,54, 0.2)",
            "rgba(34,198,246, 0.2)",
            "rgba(153, 102, 255, 0.2)",
            "rgba(255, 159, 64, 0.2)",
            "rgba(233,30,99, 0.2)",
            "rgba(205,220,57, 0.2)"

        ];
    
        $usersChart->dataset('Customer Signup Chart', 'bar', $labelval)
        ->color($borderColors)
        ->backgroundcolor($fillColors);
        return view('admin.dashboard.index', [ 'usersChart' => $usersChart ,"return_data"=>$return_data]);
    }
}
