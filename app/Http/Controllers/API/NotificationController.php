<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Faq;


class NotificationController extends Controller
{
    /*Function Used to get events*/
    public function notification(Request $request)
    {
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit?  $request->limit : 10;
        $offset = ($page - 1) * $limit;
        $notification = Notification::offset($offset)->limit($limit)->get();
        $totalrecords = Notification::all()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        if($notification->count() > 0)
        {
            unset($notification[0]->deleted_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','total'=> $totalrecords,"total_page"=> $totalpage,"page"=> $page,"limit"=> $offset,'data'=>$notification];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    public function deleteNotification(Request $request)
    {
        $cart_data = $request->getContent();
        $data = json_decode($cart_data,true);
       
        $id = $data['id'];
        if(!empty($data['id']))
        {
            $affectedRows = Notification::whereIn('id', $id)->delete();     
        }
        else
        { 
            $affectedRows = DB::table('notifications')->delete();
        }
        
        if($affectedRows)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Notification Deleted Successfully.'];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    public function faq(Request $request)
    {
        $faq = Faq::select('id','unique_id','category','title','description','created_by','created_at','updated_at')->get();

        if($faq->count()>0)
        {
            $tmp = array();
            foreach($faq as $value)
            {
                $tmp[$value->category]['category'] = $value->category;
                $tmp[$value->category]['questions'][] = array(
                        'id' =>  $value->id,
                        'title' =>  $value->title,
                        'description'=> $value->description,
                        'unique_id' =>$value->unique_id,
                        'created_by' =>$value->created_by,
                        'created_at' =>$value->created_at
                    );
            }
            $final_tmp = array();
            foreach ($tmp as $value) 
            {
                $final_tmp[] = $value;
            }
           
            $response = ['success' => true,'status' => 200,'message' => 'Faq Data Found Successfully.','data'=>$final_tmp];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }
}
