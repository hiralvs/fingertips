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

        if($request->role)
        {
            $return_data['role'] = $request->role;
        }
        else
        {
            $return_data['role'] = 'admin';
        }

        $return_data['admindata'] = User::where('role','admin')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['customerdata'] = User::where('role','customer')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['propertyadmindata'] = User::where('role','property_admin')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['brandmerchantdata'] = User::where('role','brand_merchant')->orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.user.index', array_merge($this->data, $return_data))->render();
    }

    /* Function user to add user data */
    public function adduser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
            'role' => 'required',
            'status' => 'required',
            'dob' => 'required',
            'contact' => 'required|numeric',
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
        $input['mobile'] = $input['contact'];
        $input['dob'] = date('Y-m-d',strtotime($input['dob']));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required',
            'status' => 'required',
            'dob' => 'required',
            'contact' => 'required|numeric',
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->mobile = $request->contact;
        $user->dob = date('Y-m-d',strtotime($request->dob));
        
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
        $role = $request->role;

        $user = User::where('role',$role)->where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")->paginate();
        if($user)
        {
            $data = $this->htmltoexportandsearch($user,true);
            $arr = array('status' => true,"data"=> $data);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }

        return Response()->json($arr);

    }

    public function adminexport(Request $request)
    {
        $role = $request->role;
        $search = (isset($request->search) && $request->search !="") ? $request->search : "";
        $query = User::where('role',$role);

        if($request->search != "")
        {
            $query = $query->where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%");
        }

        $finaldata = $query->get();
        $this->htmltoexportandsearch($finaldata);
    }

    public function htmltoexportandsearch($finaldata,$search=false)
    {
        $html = "";
        if(!empty($finaldata) && $finaldata->count() > 0)
        {   
            if($search==false)
            {
                  $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                            <th>Image</th>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created on</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                if($value['status'] == '0') 
                {
                    $status = "Active";
                }
                else if($value['status'] == '1'){
                    $status = "Inactive";            
                }
                if($search == true)
                {
                    if($value['profile_pic']!= null)
                    {
                        $path = asset('public/upload').'/'.$value['profile_pic'];
                        $image = '<img src="'.$path.'" alt="">';
                    }
                    else
                    {
                        $image = "";
                    }
                                     
                }
                else
                {
                    $image = $value['profile_pic'];
                }

                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr>
                    <td>".$image."</td>
                    <td>".$value['unique_id']."</td>
                    <td>".$value['name']."</td>
                    <td>".$value['email']."</td>
                    <td>".$value['gender']."</td>
                    <td>".$value['role']."</td>
                    <td>".$status."</td>
                    <td>".$cdate."</td>";
                if($search == true)
                {
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-target='#editUser".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                            <a class='delete' onclick='return confirm('Are you sure you want to delete this User?')' href='".route('user.delete', $value->id)."'><i class='mdi mdi-delete'></i></a> 
                          </td>";
                }
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="9">No Records Found</td></tr>';
        }
        if($search==false)
        {
            $html .= '</tbody></table>';
            echo $html;
        }
        else
        {
            return $html;
        }
    }
}
