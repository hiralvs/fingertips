<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use \Maatwebsite\Excel\Exporter;
use App\Brand_Connection;
use App\User;
use App\Brand;
use Datatables;
use Excel;
use App\Exports\UserExport;
use Validator;

class MallBrandsController extends Controller
{
    public function __construct(\Maatwebsite\Excel\Exporter $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;
    }
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Mall Brands');
        $return_data['meta_title'] = trans('Mall Brands');

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
        // if ($request->direction) {
        //     $direction=$request->direction;
        // } else {
        //     $direction='desc';
        // }
        $return_data['data'] = Brand_Connection::select('brands_connection.*', 'brands.id as brandid','brands.name as brandname')->leftjoin('brands', 'brands_connection.brand_id', '=', 'brands.id')->orderBy($sort)->sortable()->paginate($perpage);

        $return_data['brand_id'] = Brand::select('id', 'name')->orderBy('name', 'asc')->get();
        return View('admin.mallbrands.index', $return_data)->render();
    }
    public function addPrivacy(Request $request)
    {
        // $rules = array(
        //     'title' => 'required',
        //     'value' => 'required',
        // );
        // $customMessage = array(
        //     'title.required' => 'Title is required',
        //     'value.reuqired' => 'values must be required',
        // );

        // $validator = Validator::make($request->all(),$rules, $customMessage );

        
        // if ($validator->fails()) {
        //     return Response()->json(['errors' => $validator->errors()]);
        // }

        $request->request->remove('_token');
        $input = $request->all();
        // $input['type'] = 'privacypolicy';

        Settings::unguard();
        $check = Settings::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Settings::find($check);
            $arr = array('msg' => 'Privacy Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
}