<?php

namespace App\Http\Controllers\admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Category;
use Validator;

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
        
        $lastsegment = request()->segment(count(request()->segments()));

        $auth = Auth::user();
        $return_data = array();
        $this->data['title'] = trans('Mall Category');
        $this->data['meta_title'] = trans('Mall Category');

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
        if($lastsegment == 'eventcategory')
        {
            $return_data['title'] = trans('Event Category');
            $return_data['meta_title'] = trans('Event Category');
            $return_data['data'] = Category::where('type','event')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            // $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.eventcategory.index', $return_data)->render();
        }
        else if($lastsegment == 'mallcategory')
        {
            $return_data['title'] = trans('Mall Category');
            $return_data['meta_title'] = trans('Mall Category');
            $return_data['data'] = Category::where('type','malls')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            // $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.mallcategory.index', $return_data)->render();
        }
        else if($lastsegment == 'attractioncategory')
        {
            $return_data['title'] = trans('Attraction Category');
            $return_data['meta_title'] = trans('Attraction Category');
            $return_data['data'] = Category::where('type','attraction')->orderBy($sort, $direction)->sortable()->paginate($perpage);
            // $return_data['common_id'] = ShopsandMalls::select('id', 'name')->get();
            return View('admin.attractioncategory.index', $return_data)->render();
        }
        // $return_data['data'] = Category::where('type','malls')->orderBy($sort,$direction)->sortable()->paginate($perpage);        
        // return View('admin.mallcategory.index', array_merge($this->data, $return_data))->render();
    }


public function addCategory(Request $request)
    {
        $request->request->remove('_token');
        $request->request->remove('desc');
        $input = $request->all();
        
        if ($request->hasFile('cat_image')) {

            $image = $request->File('cat_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/category/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $input['cat_image'] = $filename;
        }
        Category::unguard();
        $check = Category::create($input)->id;
    
        $arr = array('msg' => 'Something goes to wrong. Please try again lator', 'status' => false);
        if ($check) {
            $data = Category::find($check);        
            $arr = array('msg' => 'Category Added Successfully', 'status' => true,'data'=> $data);
        }
        return Response()->json($arr);
    }
    
    /* Function used to delete ema category */
    public function delete(Request $request)
    {

        $lastsegment = request()->segments();
        if($lastsegment[0] == 'mallcategorydelete')
        {
            $lastsegment = 'mallcategory';
        }
        if($lastsegment[0] == 'eventcategorydelete')
        {
            $lastsegment = 'eventcategory';
        }
        if($lastsegment[0] == 'attractioncategorydelete')
        {
            $lastsegment = 'attractioncategory';
        }


        $query = Category::where('id',$request->id);
        $query->delete();
        return redirect()->route($lastsegment)->with('success', 'Category Deleted Successfully');
    }

    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return Response()->json(['errors' => $validator->errors()]);
        }

        

        $category = Category::find($request->id);
        $category->category_name = $request->category_name;
        $category->type = $request->type;
        if ($request->hasFile('cat_image')) 
        {
            $image = $request->File('cat_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            $path = public_path('upload/category/' . $filename);

            Image::make($image->getRealPath())->save($path);
            $category->cat_image = $filename;
        }
        Category::unguard();
        $category->save();
        if (!empty($category)) {
            $data = Category::find($request->id);
            $arr = array('msg' => '$category Updated Successfully', 'status' => true,'data'=> $data);
        } else {
            $arr = array('msg' => 'Something goes to wrong. Please try again latr', 'status' => false);
        }
        return Response()->json($arr);
    }

    // /* Function used to display ema category */
    // public function productCategory(Request $request) {
	// 	$auth = Auth::user();
    //     $return_data = array();
    //     $this->data['title'] = trans('Product Category');
    //     $this->data['meta_title'] = trans('Product Category');

    //     if($request->per_page)
    //     {
    //         $perpage = $request->per_page;
    //     }
    //     else
    //     {
    //         $perpage = 3;
    //     }

    //     if($request->sort)
    //     {
    //         $sort=$request->sort;
    //     }
    //     else
    //     {
    //         $sort='id';
    //     }

    //     if($request->direction)
    //     {
    //         $direction=$request->direction;
    //     }
    //     else
    //     {
    //         $direction='desc';
    //     }

    //     $return_data['data'] = Category::where('type','product')->orderBy($sort,$direction)->sortable()->paginate($perpage);
    //           //  return view('products',compact('products'));
        
    //     return View('admin.productcategory.index', array_merge($this->data, $return_data))->render();
    // }

}
