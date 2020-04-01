<?php
namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Help;
use Datatables;
use Validator;

class HelpController extends Controller
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
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Help Details');
        $return_data['meta_title'] = trans('Help Details');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }
        $return_data['data'] = Help::orderBy('id', 'desc')->sortable()->paginate($perpage);

        return View('admin.help.index', $return_data)->render();
    }
    public function addHelp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'contact' => 'required',
            'email' => 'required',
            'url' => 'required',
        ]);
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }
        $request->request->remove('_token');

        $input = $request->all();
        // $input['type'] = 'tax';

        Help::unguard();
        $check = Help::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Help::find($check);
        
        $arr = array('msg' => 'Help Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    public function delete(Request $request)
    {
        $query = Help::where('id', $request->id);
        $query->delete();
        return redirect()->route('help')->with('success', 'Help Deleted Successfully');
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'contact' => 'required',
            'email' => 'required',
            'url' => 'required',
        ]);
        
        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        $help = Help::find($request->id);
        $help->address =  $request->address;
        $help->contact =  $request->contact;
        $help->email =  $request->email;
        $help->url =  $request->url;

        Help::unguard();
               
        $help->save();
       
        if (!empty($help)) {
            $data = Help::find($request->id);
            $arr = array('msg' => 'Help Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
}