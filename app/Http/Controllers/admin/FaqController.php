<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Faq;
use Validator;

class FaqController extends Controller
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
        $this->data['title'] = trans('FAQ');
        $this->data['meta_title'] = trans('FAQ');

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

        $return_data['data'] = Faq::orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.faq.index', array_merge($this->data, $return_data))->render();
    }

    /* Function used to add area */
    public function addFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category' => 'required',                 
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
        $input = array();
        $input['unique_id'] = get_unique_id('brands');
        $input['category'] = $request->category;
        $input['title'] = $request->title;
        $input['description'] = $request->description;
        $input['created_by'] = $username;
        Faq::unguard();
        $check = Faq::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
	        $arr = array('msg' => 'Faq Added Successfully', 'status' => true);
        }
        return Response()->json($arr);
    }

    /* Function used to delete area */
    public function delete(Request $request)
    {
        $query = Faq::where('id',$request->id);
        $query->delete();
        return redirect()->route('faq')->with('success', 'Faq Deleted Successfully');
    }

    /* Function used to update area */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $faq = Faq::find($request->id);
        $faq->title = $request->title;
        $faq->description = $request->description;
        $faq->category = $request->category;

        Faq::unguard();
        $faq->save();
       
        if (!empty($faq)) {
            $arr = array('msg' => 'Faq Updated Successfully', 'status' => true);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }

}
