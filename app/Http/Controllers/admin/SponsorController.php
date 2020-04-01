<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Sponsor;
// use Datatables;
use App\Category;
// use App\User;
use Validator;

class SponsorController extends Controller
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
        $return_data['title'] = trans('Sponsor Listing');
        $return_data['meta_title'] = trans('Sponsor Listing');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='sponsors.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        $return_data['data'] = Sponsor::orderBy($sort, $direction)->sortable()->paginate($perpage);
        return View('admin.sponsors.index', $return_data)->render();
    }
    public function addSponsors(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'image' => 'required',
            'title' => 'required',
            'url' => 'required',
        ]);
        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $request->request->remove('_token');
        $input = $request->all();
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/sponsors/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['image'] = $filename;
        }
        Sponsor::unguard();

        $check = Sponsor::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Sponsor::find($check);
        
        $arr = array('msg' => 'Sponsor Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    /* Function used to delete shops */
    public function delete(Request $request)
    {
        $query = Sponsor::where('id',$request->id);
        $query->delete();
        return redirect()->route('sponsors')->with('success', 'Sponsor Deleted Successfully');
    }
            /* Function used to update shops */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
        ]);
        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }

        $sponsor = Sponsor::find($request->id);
        $sponsor->title = $request->title;
        $sponsor->url = $request->url;

        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/sponsors/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $sponsor->image = $filename;
        }
        Sponsor::unguard();
        $sponsor->save();
        if (!empty($sponsor)) {
            $data = Sponsor::find($request->id);
            $arr = array('msg' => 'Sponsor Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }
    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $brand = Sponsor::where('title','LIKE',"%{$search}%")
        ->orWhere('url', 'LIKE',"%{$search}%")
        ->paginate();

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