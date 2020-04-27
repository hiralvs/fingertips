<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Color;
use Datatables;
use App\User;
use Validator;


class ColorController extends Controller
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
        $return_data['title'] = trans('Color Listing');
        $return_data['meta_title'] = trans('Color Listing');

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
        $return_data['data'] = Color::orderBy($sort,$direction)->sortable()->paginate($perpage);
        return View('admin.color.index', $return_data)->render();
    }
    public function addColor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['color'] = $input['color'];
        Color::unguard();
        $check = Color::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again later', 'status' => false);
        if($check){ 
        $data = Color::find($check);
        
        $arr = array('msg' => 'Color Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function used to delete area */
    public function delete(Request $request)
    {
        $query = Color::where('id',$request->id);
        $query->delete();
        return redirect()->route('color')->with('success', 'Color Deleted Successfully');
    }

    /* Function used to update area */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $area = Color::find($request->id);
        $area->color = $request->color;

        Color::unguard();
        $area->save();
       
        if (!empty($area)) {
            $data = Color::find($request->id);
            $arr = array('msg' => 'Color Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}