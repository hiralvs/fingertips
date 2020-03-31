<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Trendingnow;
// use Datatables;
use App\Category;
// use App\User;
use Validator;

class TrendingController extends Controller
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
        $return_data['title'] = trans('Trending Listing');
        $return_data['meta_title'] = trans('Trending Listing');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='trending_now.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='desc';
        }
        $return_data['data'] = Trendingnow::orderBy($sort, $direction)->sortable()->paginate($perpage);

        return View('admin.trendingnow.index', $return_data)->render();
    }
    public function addTrending(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'image' => 'required',
            'title' => 'required',
            'link' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id('trending_now');
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/trending/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['image'] = $filename;
        }
        Trendingnow::unguard();

        $check = Trendingnow::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Trendingnow::find($check);
        
        $arr = array('msg' => 'Trending Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    /* Function used to delete shops */
    public function delete(Request $request)
    {
        $query = Trendingnow::where('id',$request->id);
        $query->delete();
        return redirect()->route('trending')->with('success', 'Trend Deleted Successfully');
    }
        /* Function used to update shops */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'link' => 'required',
            'status' => 'required',
        ]);
        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }

        $trendingnow = Trendingnow::find($request->id);
        $trendingnow->title = $request->title;
        $trendingnow->link = $request->link;
        $trendingnow->status = $request->status;

        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/trending/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $trendingnow->image = $filename;
        }
        Trendingnow::unguard();
        $trendingnow->save();
        if (!empty($trendingnow)) {
            $data = Trendingnow::find($request->id);
            $arr = array('msg' => 'Trend Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }

    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $brand = Trendingnow::where('title','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")
        ->orWhere('link', 'LIKE',"%{$search}%")->paginate();

        if($brand)
        {
            $arr = array('status' => true,"data"=>$brand[0]);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }
        return Response()->json($arr);
    }
}