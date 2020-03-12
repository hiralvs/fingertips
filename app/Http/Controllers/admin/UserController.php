<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\User;
use Validator;

class UserController extends Controller
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

    /* Function user to display user list */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $this->data['title'] = trans('User Management');
        $this->data['meta_title'] = trans('User Management');

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

        $return_data['data'] = User::orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.user.index', array_merge($this->data, $return_data))->render();
    }

    /* Function user to add user data */
    public function adduser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            //'password' => 'required|min:8|confirmed',
            'password' => 'required|min:8',
            'role' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $request->request->remove('_token');
        $input = $request->all();
        $input['password'] = bcrypt('123456');
        $input['unique_id'] =  get_unique_id('users');
        $input['role'] = $input['role'];
        $input['gender'] = $input['gender'];
        if ($request->hasFile('profile_pic')) {

            $image = $request->File('profile_pic');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['profile_pic'] = $filename;
        }
        $check = User::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = User::find($check);
        
        $arr = array('msg' => 'User Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function user to delete user data */
    public function delete(Request $request){
        $query = User::where('id',$request->id);
        $query->delete();
        return redirect()->route('usermanagement')->with('success', 'User Deleted Successfully');
    }

    public function edit(Request $request){
        $query = User::where('id',$request->id);
        return View('admin.user.edit',$query)->render();
    }
    
    /* Function user to update user data */
    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        if ($request->hasFile('profile_pic')) {

            $image = $request->File('profile_pic');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $user->profile_pic = $filename;
        }
        $user->save();
       
        if (!empty($user)) {
            $data = User::find($request->id);
            $arr = array('msg' => 'User Updated Successfully', 'status' => true,'data'=> $data);
            //return redirect()->route('user.edit', array('id' => $user->id))->with('success', trans('common.profile_update'));
        } else {
//           return redirect()->route('dashboard');
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }

    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $user = User::where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")->paginate();

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
