<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Settings;
use Datatables;
use App\User;
use Validator;

class TaxController extends Controller
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
        $this->data['title'] = trans('Tax Percentage');
        $this->data['meta_title'] = trans('Tax Percentage');

        if($request->per_page)
        {
            $perpage = $request->per_page;
        }
        else
        {
            $perpage = 10;
        }
          $return_data['data'] = Settings::orderBy('id', 'desc')->sortable()->paginate($perpage);

        // $return_data['category_id'] = Category::select('id', 'category_name')->orderBy('category_name', 'asc')->get();
        // $return_data['property_user_id'] = User::select('id', 'name')->where('role', 'property_admin')->get();
        // echo "<pre>";
        // print_r($return_data['property_user_id']);
        // exit;

              //  return view('products',compact('products'));
        
        return View('admin.tax.index', $return_data)->render();
    }
    public function addTax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Value' => 'required',          
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $request->request->remove('_token');

        $input = $request->all();
        $input['type'] = 'tax';

        Settings::unguard();
        $check = Settings::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Settings::find($check);
        
        $arr = array('msg' => 'Tax Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $query = Settings::where('id', $request->id);
        $query->delete();
        return redirect()->route('tax')->with('success', 'Tax Deleted Successfully');
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);
        
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $settings = Settings::find($request->id);
        $settings->value =  $request->value;

        Settings::unguard();
               
        $settings->save();
       
        if (!empty($settings)) {
            $data = Settings::find($request->id);
            $arr = array('msg' => 'Tax Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }

}