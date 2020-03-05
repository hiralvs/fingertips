<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use \Maatwebsite\Excel\Exporter;
use App\User;
use Datatables;
use Excel;
use App\Exports\UserExport;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Maatwebsite\Excel\Exporter $excel)
    {
        $this->middleware('auth');
        $this->excel = $excel;

    }

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
        $return_data['data'] = User::orderBy('id','desc')->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.user.index', array_merge($this->data, $return_data))->render();
    }

    public function create()
    {
    return view('admin.user.create');
    }

    public function adduser(Request $request)
    {
        $request->request->remove('_token');
        $input = $request->all();
        $input['password'] = bcrypt('123456');
        $input['unique_id'] =  get_unique_id();
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

    public function delete(Request $request){
        $query = User::where('id',$request->id);
        $query->delete();
        return redirect()->route('usermanagement')->with('success', 'User Deleted Successfully');
    }

    public function edit(Request $request){
        $query = User::where('id',$request->id);
        return View('admin.user.edit',$query)->render();
    }

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

    public function csv()
    {
       $users = User::orderBy('id','desc');
       // echo "<pre>"; print_r($users);

        // Generate and return the spreadsheet
        return Excel::download(User::get(),'users.xlsx');
    }

    public function headings(): array
    {
        return [
            'Name',
            'Surname',
            'Email',
            'Twitter',
        ];
    }


    // public function userdetails(Request $request)
    // {
    //     $auth = Auth::user();
    //     $columns = array( 
    //                         "0" =>'profile_pic', 
    //                         "1" =>'unique_id', 
    //                         "2" =>'name', 
    //                         "3" =>'email',
    //                         "4"=> 'gender',
    //                         '5'=> 'role',
    //                         '6'=> 'status',
    //                         '7'=> 'created_at',
    //                         '8'=> 'action'
    //                     );

    //     $userQuery= User::select('id','unique_id','profile_pic','name','gender','role','email','status','created_at')->orderBy('id','desc');
       
    //     $totalData = $userQuery->count();

    //     $totalFiltered = $totalData; 

    //     $limit = $request->input('length');
    //     $start = $request->input('start');
    //     $order = $columns[$request->input('order.0.column')];
    //     $dir = $request->input('order.0.dir');

    //     if(empty($request->input('search.value')))
    //     {            
    //         $userData = $userQuery->offset($start)
    //                      ->limit($limit)
    //                      ->orderBy($order,$dir);
    //     }
    //     else 
    //     {
    //         $search = $request->input('search.value'); 

    //         $userData =  $userQuery->where('name','LIKE',"%{$search}%")
    //                         ->orWhere('email', 'LIKE',"%{$search}%")
    //                         ->orWhere('unique_id', 'LIKE',"%{$search}%")
    //                         ->offset($start)
    //                         ->limit($limit)
    //                         ->orderBy($order,$dir);

    //         $totalFiltered = $userQuery->where('name','LIKE',"%{$search}%")
    //                          ->orWhere('unique_id', 'LIKE',"%{$search}%")
    //                          ->orWhere('email', 'LIKE',"%{$search}%")
    //                          ->count();
    //     }
    //     $userData = $userQuery->get();
    //     $data = array();
    //     if(!empty($userData))
    //     {
    //         $status_array = trans('common.label_common_status_array');
    //         foreach ($userData as $users)
    //         {
    //             $nestedData['profile_pic'] = $users->profile_pic;
    //             $nestedData['unique_id'] = $users->unique_id;
    //             $nestedData['name'] = $users->name;
    //             $nestedData['email'] = $users->email;
    //             $nestedData['gender'] =$users->gender;
    //             $nestedData['role'] =$users->role;

    //             $nestedData['created_at'] = date("d F Y",strtotime($users->created_at));

    //             $nestedData['status'] = '';
    //             if($users->status == '1')
    //             {
    //                 $nestedData['status'] = 'Inactive';
    //             }
    //             else
    //             {
    //                 $nestedData['status'].='Active';
    //             }                                            
              
    //             $nestedData['action'] = '<a href="'.route('user.delete',['id'=>$users->id]).'"  class="delete" title="Delete" class="btn btn-default btn-xs" ><i class="fa fa-times text-danger text"></i></a>';
    //             $data[] = $nestedData;

    //         }
    //     }
          
    //     $json_data = array(
    //                 "draw"            => intval($request->input('draw')),  
    //                 "recordsTotal"    => intval($totalData),  
    //                 "recordsFiltered" => intval($totalFiltered), 
    //                 "data"            => $data   
    //                 );
            
    //     echo json_encode($json_data); 
    // }

}
