<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\ShopsandMalls;
use App\User;
use App\Category;
use App\Area;


class ShopsandMallsController extends Controller
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

    /* Function used to display shops and malls */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Malls');
        $return_data['meta_title'] = trans('Malls');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 3;
        }

        if($request->sort)
        {
            $sort=$request->sort;
        }
        else
        {
            $sort='shopsandmalls.id';
        }

        if($request->direction)
        {
            $direction=$request->direction;
        }
        else
        {
            $direction='desc';
        }

        $return_data['data'] = ShopsandMalls::select('shopsandmalls.*','users.id as userid','users.name as propertyadmin')->leftjoin('users', 'shopsandmalls.property_admin_user_id', '=', 'users.id')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        
        $return_data['property_admin'] = User::select('id', 'name')->where('role','property_admin')->get();
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name','asc')->get();
        $return_data['area'] = Area::select('id', 'area_name')->orderBy('area_name','asc')->get();
        
        return View('admin.malls.index',$return_data)->render();
    }

    /* Function used to add area */
    public function addShopsandMalls(Request $request)
    {
        $user = Auth::user();
        $username = "";
        if(!empty($user))
        {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $request->request->remove('lat');
        $request->request->remove('long');
        $request->request->remove('desc');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id("shopsandmalls");
        $input['category_id'] =  implode(",",$input['category_id']);
        $input['created_by'] =  $username ;
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/malls/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['image'] = $filename;
        }
        ShopsandMalls::unguard();
        $check = ShopsandMalls::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data['malls'] = ShopsandMalls::find($check);
        $data['propertyadmin'] = User::select('name as propertyadmin')->find($data['malls']->property_admin_user_id);
        
        $arr = array('msg' => 'Mall Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function used to delete area */
    public function delete(Request $request)
    {
        $query = ShopsandMalls::where('id',$request->id);
        $query->delete();
        return redirect()->route('shopsmalls')->with('success', 'Malls Deleted Successfully');
    }

    /* Function used to update area */
    public function update(Request $request)
    {
        $malls = ShopsandMalls::find($request->id);
        $categoryid = implode(",",$request->category_id);
        $malls->name =  $request->name;
        $malls->location =  $request->location;
        $malls->openinghrs =  $request->openinghrs;
        $malls->closinghrs =  $request->closinghrs;
        $malls->contact =  $request->contact;
        $malls->property_admin_user_id =  $request->property_admin_user_id;
        $malls->category_id =  $categoryid;
        $malls->area_id =  $request->area_id;
        $malls->featured_mall =  $request->featured_mall;
        $malls->type =  $request->type;
        $malls->description =  $request->description;
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/malls/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $malls->image = $filename;
        }
        ShopsandMalls::unguard();
        $malls->save();
       
        if (!empty($malls)) {
            $data = ShopsandMalls::find($request->id);
            $arr = array('msg' => 'Malls Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }

     /* Function user to search user data */
     public function search(Request $request)
     {
         $search = $request->input('search');
 
         $user = ShopsandMalls::where('name','LIKE',"%{$search}%")
         ->orWhere('unique_id', 'LIKE',"%{$search}%")
         ->orWhere('location', 'LIKE',"%{$search}%")
         ->orWhere('type', 'LIKE',"%{$search}%")
         ->orWhere('openinghrs', 'LIKE',"%{$search}%")
         ->orWhere('contact', 'LIKE',"%{$search}%")
         ->orWhere('featured_mall', 'LIKE',"%{$search}%")->paginate();
 
         if($user)
         {
             $arr = array('status' => true,"data"=>$user[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}
