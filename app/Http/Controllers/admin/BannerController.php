<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Banner;
use Datatables;
use App\User;
use Validator;


class BannerController extends Controller
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
        $this->data['title'] = trans('Banner Listing');
        $this->data['meta_title'] = trans('Banner Listing');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }
        $return_data['data'] = Banner::orderBy('id','desc')->sortable()->paginate($perpage);
        // $return_data['category_id'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        $return_data['property_user_id'] = User::select('id', 'name')->where('role', 'property_admin')->get();
        // echo "<pre>";
        // print_r($return_data['property_user_id']);
        // exit;

              //  return view('products',compact('products'));
        
        return View('admin.banner.index', $return_data)->render();
    }
    public function addBanner(Request $request)
    {
                $input = $request->all();

        $validator = Validator::make($request->all(), [
            'location' => 'required',
            'bannerimage' => 'required',
            'type' => 'required',          
        ]);
        $validator->sometimes('ema', 'required', function ($input) {
            return $input->type == 'inapp';
        });
        $validator->sometimes('property_user_id', 'required', function ($input) {
            return $input->type == 'inapp';
        });
        $validator->sometimes('url', 'required', function ($input) {
            return $input->type == 'outsideapp';
        });


        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $request->request->remove('_token');
        $input = $request->all();
        // $input['banner'] = $input['category_name'];
        $input['type'] = $input['type'];
        if ($request->hasFile('bannerimage')) {

            $image = $request->File('bannerimage');
            $filename = 'banner'.time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/banners/' . $filename);
            // $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['bannerimage'] = $filename;
        }
        Banner::unguard();
        $check = Banner::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Banner::find($check);
        
        $arr = array('msg' => 'Banner Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request){
        $query = Banner::where('id',$request->id);
        $query->delete();
        return redirect()->route('banner')->with('success', 'Banner Deleted Successfully');
    }
    public function edit(Request $request){
        $query = Banner::where('id',$request->id);
        return View('admin.banner.edit',$query)->render();
    }
 
    public function update(Request $request)
    {
        // $input = $request->all();

        $validator = Validator::make($request->all(), [
            'location' => 'required',
            'type' => 'required',
        ]);
        $validator->sometimes('ema', 'required', function ($input) {
            return $input->type == 'inapp';
        });
        $validator->sometimes('property_user_id', 'required', function ($input) {
            return $input->type == 'inapp';
        });
        $validator->sometimes('url', 'required', function ($input) {
            return $input->type == 'outsideapp';
        });
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        
        $banner = Banner::find($request->id);
        $banner->location = $request->location;
        $banner->url = $request->url;
        $banner->type = $request->type;
        $banner->ema = $request->ema;
        $banner->property_user_id = $request->property_user_id;

        if ($request->hasFile('bannerimage')) 
        {
            $image = $request->File('bannerimage');
            $filename = 'banner'.time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/banner/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $banner->bannerimage = $filename;
        }
        Banner::unguard();
               
        $banner->save();
       
        if (!empty($banner)) {
            $data = Banner::find($request->id);
            $arr = array('msg' => 'Banner Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}
