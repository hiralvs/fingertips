<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\FlashSale;
use App\Category;
use App\Settings;
use App\ShopsandMalls;
use DB;
use Validator;

class FlashsaleController extends Controller
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
        $return_data['title'] = trans('Flashsale');
        $return_data['meta_title'] = trans('Flashsale');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='flash_sales.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        $return_data['data'] = FlashSale::orderBy('id', 'desc')->sortable()->paginate($perpage);
                
        return View('admin.flashsale.index', $return_data)->render();
    }
}