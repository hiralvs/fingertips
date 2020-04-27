<?php

namespace App\Http\Controllers\admin;


use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $user = Auth::user();
    }
    
    /* Function used to display trending */
    public function index(Request $request)
    {
        $auth = Auth::user();
        $return_data = array();
        $return_data['title'] = trans('Orders');
        $return_data['meta_title'] = trans('Orders');

        if ($request->per_page) {
            $perpage = $request->per_page;
        } else {
            $perpage = 10;
        }

        if ($request->sort) {
            $sort=$request->sort;
        } else {
            $sort='orders.id';
        }

        if ($request->direction) {
            $direction=$request->direction;
        } else {
            $direction='asc';
        }
        $return_data['data'] = Order::select('orders.id','orders.order_id','orders.order_bill_no','orders.product_id','orders.amount','orders.status','orders.order_by','products.unique_id','products.name as product_name','brands.name as brand_name','orders.created_at')->join('products','products.id','=','orders.product_id')->join('brands','products.brand_id','=','brands.id')->orderBy('id', 'desc')->sortable()->paginate($perpage);
        return View('admin.orders.index', $return_data)->render();
    }

    /* Function used to delete shops */
    public function status(Request $request)
    {
    	$orders = Order::find($request->id);

    	$orders->status = $request->status;
    	
    	$affectedrows = $orders->save();
    	if($affectedrows)
    	{
    		$arr = array('msg' => 'Order Status Changed Successfully', 'status' => true,'data'=> []);
        }
        else
        {
        	$arr = array('msg' => 'Order Status Not Changed', 'status' => false,'data'=> []);
        }
        return Response()->json($arr);
    }

    /* Function user to search user data */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $orders =  Order::select('orders.id','orders.order_id','orders.order_bill_no','orders.product_id','orders.amount','orders.status','orders.order_by','products.unique_id','products.name as product_name','brands.name as brand_name','orders.created_at')->join('products','products.id','=','orders.product_id')->join('brands','products.brand_id','=','brands.id')->where('orders.order_id','LIKE',"%{$search}%")
        ->orWhere('orders.order_bill_no','LIKE',"%{$search}%")
        ->orWhere('orders.product_id', 'LIKE',"%{$search}%")
        ->orWhere('products.unique_id', 'LIKE',"%{$search}%")
        ->orWhere('products.name', 'LIKE',"%{$search}%")
        ->orWhere('brands.name', 'LIKE',"%{$search}%")
        ->orWhere('orders.order_id', 'LIKE',"%{$search}%")
        ->orWhere('orders.status', 'LIKE',"%{$search}%")->paginate();

        if($orders)
        {
        $data = $this->htmltoexportandsearch($orders,true);
        $arr = array('status' => true,"data"=>$data);    
        }
        else{
         $arr = array('status' => false,"msg"=>"Data Not Found","data"=>[]);    
        }

        return Response()->json($arr);

    }

    public function export(Request $request)
    {
        $search = (isset($request->search) && $request->search !="") ? $request->search : "";
        $query = Order::select('orders.id','orders.order_id','orders.order_bill_no','orders.product_id','orders.amount','orders.status','orders.order_by','products.unique_id','products.name as product_name','brands.name as brand_name','orders.created_at')->join('products','products.id','=','orders.product_id')->join('brands','products.brand_id','=','brands.id');

        if($request->search != "")
        {
            $query = $query->where('orders.order_id','LIKE',"%{$search}%")
        ->orWhere('orders.order_bill_no','LIKE',"%{$search}%")
        ->orWhere('orders.product_id', 'LIKE',"%{$search}%")
        ->orWhere('products.unique_id', 'LIKE',"%{$search}%")
        ->orWhere('products.name', 'LIKE',"%{$search}%")
        ->orWhere('brands.name', 'LIKE',"%{$search}%")
        ->orWhere('orders.order_id', 'LIKE',"%{$search}%")
        ->orWhere('orders.status', 'LIKE',"%{$search}%");
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
                  $html .='<table class="table table-hover" id="orderstableData">
                      <thead>
                        <tr>
                        	<th>Order Id</th>
                          <th>Order Item No</th>
                          <th>Product Id</th>
                          <th>Product Name</th>
                          <th>Brand Name</th>
                          <th>Customer Name</th>
                          <th>Amount</th>
                          <th>Order Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>';  
            } 
            
            foreach ($finaldata as $key => $value) 
            {
                $cdate = date('d F Y',strtotime($value['created_at']));
                $html .="<tr><td>".$value['order_id']."</td><td>".$value['order_bill_no']."</td><td>".$value['unique_id'] ."</td><td>".$value['product_name']."</td><td>".$value['brand_name']."</td><td>".$value['order_by']."</td><td>".$value['amount']."</td><td>".$cdate."</td><td>".$value['status']."</td>";
                $html.="</tr>";
            }
        }
        else
        {
            $html .= '<tr><td colspan="10">No Records Found</td></tr>';
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
