<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Attractions;
use Datatables;
use App\Category;
use App\Area;
use App\User;
use Validator;


class AttractionController extends Controller
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

    /* Function used to display attraction */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Attraction Listing');
        $return_data['meta_title'] = trans('Attraction Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }

        if($request->sort)
        {
            $sort=$request->sort;
        }
        else
        {
            $sort='attractions.id';
        }

        if($request->direction)
        {
            $direction=$request->direction;
        }
        else
        {
            $direction='desc';
        }
        $return_data['data'] = Attractions::select('attractions.*', 'users.id as userid', 'users.name as propertyadmin')->leftjoin('users', 'attractions.property_admin_user_id', '=', 'users.id')->orderBy($sort, $direction)->sortable()->paginate($perpage);
        $return_data['property_admin'] = User::select('id', 'name')->where('role','property_admin')->get();
        $return_data['category'] = Category::select('id', 'category_name')->orderBy('category_name','asc')->get();
        $return_data['area'] = Area::select('id', 'area_name')->orderBy('area_name','asc')->get();        
        return View('admin.attraction.index',$return_data)->render();
    }
    public function addAttractions(Request $request) {
            $validator = Validator::make($request->all(), [
            'attraction_image' => 'required',
            'property_admin_user_id' => 'required',
            'category_id' => 'required',
            'area_id' => 'required',
            'featured_mall' => 'required',
            'cost' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }


        $user = Auth::user();
        $username = "";
        if (!empty($user)) {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $request->request->remove('lat');
        $request->request->remove('long');
        $request->request->remove('desc');
        $input = $request->all();
        $input['unique_id'] =  get_unique_id("attractions");
        $input['category_id'] =  implode(",", $input['category_id']);
        $input['created_by'] =  $username ;
        if ($request->hasFile('attraction_image')) {
            $image = $request->File('attraction_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/attractions/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['attraction_image'] = $filename;
        }
        Attractions::unguard();
        $check = Attractions::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Attractions::find($check);
            // $data['propertyadmin'] = User::select('name as propertyadmin')->find($data['events']->property_admin_user_id);
        
            $arr = array('msg' => 'Attractions Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);

     }
    /* Function used to delete attraction */
    public function delete(Request $request)
    {
        $query = Attractions::where('id',$request->id);
        $query->delete();
        return redirect()->route('attractions')->with('success', 'Attraction Deleted Successfully');
    }

    /* Function used to update attraction */
    public function update(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'property_admin_user_id' => 'required',
            'category_id' => 'required',
            'area_id' => 'required',
            'featured_mall' => 'required',
            'cost' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $attraction = Attractions::find($request->id);
        $categoryid = implode(",",$request->category_id);
        $attraction->attraction_name =  $request->attraction_name;
        $attraction->location =  $request->location;
        $attraction->opening_time =  $request->opening_time;
        $attraction->closing_time =  $request->closing_time;
        $attraction->contact =  $request->contact;
        $attraction->property_admin_user_id =  $request->property_admin_user_id;
        $attraction->category_id =  $categoryid;
        $attraction->cost =  $request->cost;
        $attraction->area_id =  $request->area_id;
        $attraction->featured_mall =  $request->featured_mall;
        $attraction->booking_allowed =  $request->booking_allowed;
        $attraction->description =  $request->description;
        if ($request->hasFile('attraction_image')) {

            $image = $request->File('attraction_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/attraction/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $attraction->attraction_image = $filename;
        }
        Attractions::unguard();
        $attraction->save();
       
        if (!empty($attraction)) {
            $data = Attractions::find($request->id);
            $arr = array('msg' => 'attractions Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
    /* Function user to search attraction data */
     public function search(Request $request)
     {
        $search = $request->input('search');
     
        $attraction = Attractions::where('attraction_name','LIKE',"%{$search}%")
         ->orWhere('unique_id', 'LIKE',"%{$search}%")
         ->orWhere('location', 'LIKE',"%{$search}%")
         ->orWhere('opening_time', 'LIKE',"%{$search}%")
         ->orWhere('closing_time', 'LIKE',"%{$search}%")
         ->orWhere('area_id', 'LIKE',"%{$search}%")
         ->orWhere('featured_mall', 'LIKE',"%{$search}%")
         ->orWhere('created_at', 'LIKE',"%{$search}%")
         ->paginate();
 
        if($attraction)
         {
             $arr = array('status' => true,"data"=>$attraction[0]);    
         }
         else{
             $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
         }
 
         return Response()->json($arr);
 
     }
}