<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Category;


class CategoryController extends Controller
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

    /* Function used to display ema category */
    public function index(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $this->data['title'] = trans('Ema Category');
        $this->data['meta_title'] = trans('Ema Category');

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

        $return_data['data'] = Category::where('type','ema')->orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.emacategory.index', array_merge($this->data, $return_data))->render();
    }

    /* Function used to add ema category */
    public function addCategory(Request $request)
    {
        $request->request->remove('_token');
        $input = $request->all();
        $input['category_name'] = $input['category_name'];
        $input['type'] = $input['type'];
        if ($request->hasFile('cat_image')) {

            $image = $request->File('cat_image');
            $filename = 'ema'.time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/category/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $input['cat_image'] = $filename;
        }
        Category::unguard();
        $check = Category::create($input)->id;

        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if($check){ 
        $data = Category::find($check);
        
        $arr = array('msg' => 'Category Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }

    /* Function used to delete ema category */
    public function delete(Request $request)
    {
        $query = Category::where('id',$request->id);
        $query->delete();
        return redirect()->route('emacategory')->with('success', 'Category Deleted Successfully');
    }

    /* Function used to update ema category */
    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $category->category_name = $request->category_name;
        $category->type = $request->type;
        if ($request->hasFile('cat_image')) 
        {
            $image = $request->File('cat_image');
            $filename = 'ema'.time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/category/' . $filename);

            Image::make($image->getRealPath())->resize(50, 50)->save($path);
            $category->cat_image = $filename;
        }
        Category::unguard();
        $category->save();
       
        if (!empty($category)) {
            $data = Category::find($request->id);
            $arr = array('msg' => 'Category Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        }
        return Response()->json($arr);
    }
    
    /* Function used to display ema category */
    public function productCategory(Request $request) {
		$auth = Auth::user();
        $return_data = array();
        $this->data['title'] = trans('Product Category');
        $this->data['meta_title'] = trans('Product Category');

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

        $return_data['data'] = Category::where('type','product')->orderBy($sort,$direction)->sortable()->paginate($perpage);
              //  return view('products',compact('products'));
        
        return View('admin.productcategory.index', array_merge($this->data, $return_data))->render();
    }

}
