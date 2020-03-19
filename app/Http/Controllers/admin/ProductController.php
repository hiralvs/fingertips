<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\User;
use App\Brand;
use DB;

class ProductController extends Controller
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
        $return_data['title'] = trans('Product Listing');
        $return_data['meta_title'] = trans('Product Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
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
        $return_data['data'] = Product::orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['brands'] = Brand::select('id', 'name')->get();
        // echo "<pre>";
        // print_r($return_data['category_id']);
        // exit;
        return View('admin.product.index', $return_data)->render();
    }
}
