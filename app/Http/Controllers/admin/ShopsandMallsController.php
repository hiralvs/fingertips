<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\User;
use App\ShopsandMalls;
use App\Category;
use App\Area;
use Validator;

class ShopsandMallsController extends Controller
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
        $this->data['title'] = trans('ShopsandMalls');
        $this->data['meta_title'] = trans('ShopsandMalls');

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
            $return_data['type'] = $request->type;
        }
        else
        {
            $return_data['type'] = 'mall';
        }
        $return_data['malldata'] = ShopsandMalls::where('type','mall')->orderBy($sort,$direction)->sortable()->paginate($perpage);
        $return_data['shopdata'] = ShopsandMalls::where('type','shop')->orderBy($sort,$direction)->sortable()->paginate($perpage);

        $return_data['property_admin'] = User::select('id', 'name')->where('role', 'property_admin')->get();
        $return_data['category'] = Category::select('id', 'category_name')->where('type', 'malls')->orderBy('category_name', 'asc')->get();
        $return_data['area'] = Area::select('id', 'area_name')->orderBy('area_name', 'asc')->get();
        
        return View('admin.malls.index', array_merge($this->data, $return_data))->render();
    }
        public function addShopsandMalls(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
            'name' => 'required|max:255',
            'openinghrs' => 'required',
            //'closinghrs' => 'required|after:openinghrs',
            'closinghrs' => 'required',
            'property_admin' => 'required',
            'filter' => 'required',
            'area' => 'required',
            'layer' => 'required',
            'featured_mall' => 'required',
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $username = "";
        if(!empty($user))
        {
            $username = $user->name;
        }
        $request->request->remove('_token');
        $request->request->remove('lat');
        $request->request->remove('long');
        $request->request->remove('desc');
        $input = array(
            'unique_id' => get_unique_id("shopsandmalls") ,
            'name' => $request->name ,
            'location' => $request->location ,
            'latitude' => $request->latitude ,
            'longitude' => $request->longitude ,
            'openinghrs' => $request->openinghrs ,
            'closinghrs' => $request->closinghrs ,
            'contact' => $request->contact ,
            'featured_mall' => $request->featured_mall ,
            'type' => $request->layer ,
            'category_id' => implode(",", $request->filter),
            'created_by' => $username,
            'property_admin_user_id' =>  $request->property_admin ,
            'area_id' =>  $request->area ,
            'description' =>  $request->description ,
        );

        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/malls' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['image'] = $filename;
        }
        ShopsandMalls::unguard();
        $check = ShopsandMalls::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = ShopsandMalls::find($check);
        
        $arr = array('msg' => 'User Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
        public function delete(Request $request){
        $query = ShopsandMalls::where('id',$request->id);
        $query->delete();
        return redirect()->route('malls')->with('success', 'Mall Deleted Successfully');
    }
    public function edit(Request $request){
        $query = ShopsandMalls::where('id',$request->id);
        return View('admin.malls.edit',$query)->render();
    }
    
    /* Function user to update user data */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image',
            'name' => 'required|max:255',
            'openinghrs' => 'required',
            'layer' => 'required',
            'closinghrs' => 'required',
            'property_admin' => 'required',
            'filter' => 'required',
            'area' => 'required',
            'featured_mall' => 'required',
        ]);

        if($validator->fails()){
            return Response()->json(['errors' => $validator->errors()]);      
        }
        $malls = ShopsandMalls::find($request->id);
        $categoryid = implode(",", $request->filter);
        $malls->name =  $request->name;
        $malls->location =  $request->location;
        $malls->latitude =  $request->latitude;
        $malls->longitude =  $request->longitude;
        $malls->openinghrs =  $request->openinghrs;
        $malls->closinghrs =  $request->closinghrs;
        $malls->contact =  $request->contact;
        $malls->property_admin_user_id =  $request->property_admin;
        $malls->category_id =  $categoryid;
        $malls->area_id =  $request->area;
        $malls->featured_mall =  $request->featured_mall;
        $malls->type =  $request->layer;
        $malls->description =  $request->description;

        
        if ($request->hasFile('image')) {

            $image = $request->File('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/malls/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $malls->image = $filename;
        }
        ShopsandMalls::unguard();
        $malls->save();
       
        if (!empty($malls)) {
            $data = ShopsandMalls::find($request->id);
            $arr = array('msg' => 'Malls Updated Successfully', 'status' => true,'data'=> $data);
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
        $role = $request->type;

        $shopmall = ShopsandMalls::where('type',$role)->where('name','LIKE',"%{$search}%")
        ->orWhere('unique_id', 'LIKE',"%{$search}%")
        ->orWhere('location', 'LIKE',"%{$search}%")
        ->orWhere('featured_mall', 'LIKE',"%{$search}%")->paginate();
        if($shopmall)
        {
            $data = $this->htmltoexportandsearch($shopmall,true);
            $arr = array('status' => true,"data"=> $data);    
        }
        else{
            $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }

        return Response()->json($arr);

    }
    public function export(Request $request)
    {
        $type = $request->type;
        $search = (isset($request->search) && $request->search !="") ? $request->search : "";
        $query = ShopsandMalls::where('type',$type);

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
        if (!empty($finaldata) && $finaldata->count() > 0) {
            if ($search==false) {
                $html .='<table class="table table-hover" id="brandData">
                      <thead>
                        <tr>
                            <th>Id</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Opening Hours</th>
                            <th>Contact Info</th>
                            <th>Mall Type</th>
                            <th>Mall Admin</th>
                            <th>Created on</th>
                            <th>Created By</th>
                        </tr>
                      </thead>
                      <tbody>';
            }
            
            foreach ($finaldata as $key => $value) {
                if ($search == true) {
                    if ($value['image']!= null) {
                        $path = asset('public/upload/malls').'/'.$value['image'];
                        $image = '<img src="'.$path.'" alt="">';
                    } else {
                        $image = "";
                    }
                } else {
                    $image = $value['image'];
                }

                $cdate = date('d F Y', strtotime($value['created_at']));
                $html .="<tr>
                <td>".$value['unique_id']."</td>
                    <td>".$image."</td>
                    <td>".$value['name']."</td>
                    <td>".$value['location']."</td>
                    <td>".$value['openinghrs']."</td>
                    <td>".$value['contact']."</td>
                    <td>".$value['type']."</td>
                    <td>".$value['propertyadmin']."</td>
                    <td>".$cdate."</td>
                    <td>".$value['created_by']."</td>";

                if ($search == true) {
                    $vcount = $value->productvariantcount > 1 ? '1' : '0';
                    $checked =  $value->productvariantcount > 1 ? 'checked' : '' ;
                    $style = $value->productvariantcount > 1 ? 'display: block;' : 'display: none';
                    //echo "if";
                    $html .="<td><a class='edit open_modal' data-toggle='modal' data-id='".$value->id."' data-target='#editShopsandmalls".$value->id."' ><i class='mdi mdi-table-edit'></i></a> 
                          <a class='delete' onclick='return confirm('Are you sure you want to delete this Mall?')' href='".route('shopsmalls.delete', $value->id)."'><i class='mdi mdi-delete'></i></a> 
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
