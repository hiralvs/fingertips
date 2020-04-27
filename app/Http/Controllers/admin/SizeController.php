<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Size;
use Datatables;
use App\User;
use Validator;


class SizeController extends Controller
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
        $return_data['title'] = trans('Size Listing');
        $return_data['meta_title'] = trans('Size Listing');

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
            $sort='id';
        }

        if($request->direction)
        {
            $direction=$request->direction;
        }
        else
        {
            $direction='desc';
        }

        // $return_data['data'] = Color::select('color.id', 'color.created_at')->leftjoin('users','users.id','=','rewards.user_id')->orderBy('users.id','desc')->sortable()->paginate($perpage);
        $return_data['data'] = Size::orderBy($sort,$direction)->sortable()->paginate($perpage);
        return View('admin.size.index', $return_data)->render();
    }
    public function addSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['size'] = $input['size'];
        Size::unguard();
        
        $check = Size::create($input)->id;
        $arr = array('msg' => 'Something goes to wrong. Please try again later', 'status' => false);
        if($check){ 
        $data = Size::find($check);
        
        $arr = array('msg' => 'Size Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function used to delete area */
    public function delete(Request $request)
    {
        $query = Size::where('id',$request->id);
        $query->delete();
        return redirect()->route('size')->with('success', 'Size Deleted Successfully');
    }

    /* Function used to update area */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $area = Size::find($request->id);
        $area->size = $request->size;

        Size::unguard();
        $area->save();
       
        if (!empty($area)) {
            $data = Size::find($request->id);
            $arr = array('msg' => 'Size Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}