<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Area;
use Validator;


class AreaController extends Controller
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

    /* Function used to display area */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $this->data['title'] = trans('Area');
        $this->data['meta_title'] = trans('Area');

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

        $return_data['data'] = Area::orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.area.index', array_merge($this->data, $return_data))->render();
    }

    /* Function used to add area */
    public function addArea(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_name' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['area_name'] = $input['area_name'];
        Area::unguard();
        $check = Area::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Area::find($check);
        
        $arr = array('msg' => 'Area Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function used to delete area */
    public function delete(Request $request)
    {
        $query = Area::where('id',$request->id);
        $query->delete();
        return redirect()->route('area')->with('success', 'Area Deleted Successfully');
    }

    /* Function used to update area */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_name' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $area = Area::find($request->id);
        $area->area_name = $request->area_name;

        Area::unguard();
        $area->save();
       
        if (!empty($area)) {
            $data = Area::find($request->id);
            $arr = array('msg' => 'Area Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}
