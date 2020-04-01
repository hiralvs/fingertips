<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use App\User;
use App\ShopsandMalls;
use App\Attractions;
use App\Events;
use App\Highlights;
use App\FlashSale;
use App\Banner;
use App\Slider;
use App\Brand_connection;
use App\Brand;
use App\Product;
use App\Photos;
use App\Map_images;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;



class HomePageController extends Controller
{
    /*Function Used to get events*/
    public function eventListing(Request $request)
    {
        $url =env('APP_URL'); 
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit?  $request->limit : 10;
        $offset = ($page - 1) * $limit;

        $events = Events::select('id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"))->offset($offset)->limit($limit)->get();
        $totalrecords = Events::all()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);

        if($events->count() > 0)
        {

            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.', 'total'=> $totalrecords,"total_page"=> $totalpage,"page"=> $page,"limit"=> $offset,'data'=>$events];
        }
        else
        {  
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    /*Function Used to get malls*/
    public function mallListing(Request $request)
    {
        $url =env('APP_URL');
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit?  $request->limit : 10;
        $offset = ($page - 1) * $limit;
        $malls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude','type',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->offset($offset)->limit($limit)->get();
        $totalrecords = ShopsandMalls::all()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        if($malls->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.', 'total'=> $totalrecords,"total_page"=> $totalpage,"page"=> $page,"limit"=> $offset,'data'=>$malls];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

   /*Function Used to get attraction*/
    public function attractionListing(Request $request)
    {
        $url =env('APP_URL');
        $page = $request->page ? $request->page : 1;
        $limit = $request->limit?  $request->limit : 10;
        $offset = ($page - 1) * $limit;
        $attraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->offset($offset)->limit($limit)->get();
        $totalrecords = Attractions::all()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        if($attraction->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.', 'total'=> $totalrecords,"total_page"=> $totalpage,"page"=> $page,"limit"=> $offset,'data'=>$attraction];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }


    /*Function Used to get featured events*/
    public function featuredEvents(Request $request)
    {
        $url =env('APP_URL');
        $featuredevents = Events::select('id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"))->where('featured_event','yes')->paginate(20);
        if($featuredevents->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$featuredevents];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    /*Function Used to get featured shops*/
    public function featuredShops(Request $request)
    {
        $url =env('APP_URL');
        $featuredShops = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->where('featured_mall','yes')->where('type','shop')->limit(20)->get();
        if($featuredShops->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$featuredShops];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    /*Function Used to get featured malls*/
    public function featuredMalls(Request $request)
    {
        $url =env('APP_URL');
        $featuredmalls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->where('featured_mall','yes')->where('type','mall')->limit(20)->get();
        if($featuredmalls->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$featuredmalls];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found'];  
        }
        return response()->json($response);
    }

    /*Function Used to get featured attraction*/
    public function featuredAttraction(Request $request)
    {
        $url =env('APP_URL');
        $featuredAttraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->where('featured_mall','yes')->limit(20)->get();
        if($featuredAttraction->count() > 0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$featuredAttraction];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /*Function Used to get featrued attraction*/
    public function highlights(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id');
        $type = $request->route('type');
        $highlights = array();
        if($type == 'event')
        {
            $highlights = Highlights::select('highlights.*','events.description','events.location','events.latitude','events.longitude',DB::raw("CONCAT('','$url/public/upload/highlights/',highlights.image) as image"))->leftjoin('events','events.id','=','highlights.common_id')->where(['common_id'=>$id,'type'=>$type])->get();
        }
        if($type == 'malls')
        {
            $highlights = Highlights::select('highlights.*','shopsandmalls.description','shopsandmalls.location','shopsandmalls.latitude','shopsandmalls.longitude',DB::raw("CONCAT('','$url/public/upload/highlights/',highlights.image) as image"))->leftjoin('shopsandmalls','shopsandmalls.id','=','highlights.common_id')->where(['common_id'=>$id,'highlights.type'=>$type])->get();
        }
        if($type == 'attraction')
        {
            $highlights = Highlights::select('highlights.*','attractions.description','attractions.location','attractions.latitude','attractions.longitude',DB::raw("CONCAT('','$url/public/upload/highlights/',highlights.image) as image"))->leftjoin('attractions','attractions.id','=','highlights.common_id')->where(['common_id'=>$id,'highlights.type'=>$type])->get();
        }
        
        if($highlights->count() > 0)
        {
            unset($highlights[0]->deleted_at,$highlights[0]->updated_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$highlights];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /*Function Used to get featrued attraction*/
    public function flashsale(Request $request)
    {
        $url =env('APP_URL');
        $flashsale = FlashSale::select('flash_sales.*',DB::raw("CONCAT('','$url/public/upload/flashsale/',image) as image"))->get();
        if($flashsale->count() > 0)
        {
            $flashsale[0]->total_revenue = is_null($flashsale[0]->total_revenue) ? "":$flashsale[0]->total_revenue;
            unset($flashsale[0]->deleted_at,$flashsale[0]->updated_at,$flashsale[0]->created_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$flashsale];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /*Function Used to get particular homepage for event,malls and attraction*/
    public function homepagedetails(Request $request)
    {
        $url =env('APP_URL');
        $success = array();
        $type = $request->route('type'); // type can be event, malls and attraction
        $banners = Banner::select('banners.id','banners.type',DB::raw('IFNULL(banners.url , "" ) as url'),DB::raw('IFNULL(banners.ema , "" ) as ema'),DB::raw('IFNULL(banners.property_user_id , "" ) as property_user_id'),DB::raw("CONCAT('','$url/public/upload/banners/',bannerimage) as bannerimage"))->where('location',$type)->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->join('brands','products.brand_id','=','brands.id')->join('brands_connection','brands_connection.brand_id','=','brands.id')->where(["brands_connection.type"=>$type])->groupBy('products.id')->get();
        if($banners->count() > 0)
        {
            // $banners[0]->url = is_null($banners[0]->url) ? "":$banners[0]->url;
            // $banners[0]->ema = is_null($banners[0]->ema) ? "":$banners[0]->ema;
            // $banners[0]->property_user_id = is_null($banners[0]->property_user_id) ? "":$banners[0]->property_user_id;
            // unset($banners[0]->deleted_at,$banners[0]->updated_at);
            $success['banners'] = $banners;
        }
        if($deals->count()>0)
        {
            $success['deals'] =  $deals ;
        }
        if($type == 'event')
        {
            $featuredevents = Events::select('id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"))->where('featured_event','yes')->limit(20)->get();
            $success['featuredevents'] =$featuredevents;
        }
        if($type == 'malls')
        {
            $featuredShops = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->where('featured_mall','yes')->where('type','shop')->limit(20)->get();
            $featuredmalls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->where('featured_mall','yes')->where('type','mall')->limit(20)->get();
            $success['featuredShops'] = $featuredShops;
            $success['featuredmalls'] = $featuredmalls;
        }
        if($type == 'attraction')
        {
            $featuredAttraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->where('featured_mall','yes')->limit(20)->get();
            $success['featuredAttraction'] =  $featuredAttraction ;
        }
        // print_r( $success);
        // echo $success->count();
        if($success)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }

        return response()->json($response);
    }

    /*Function Used to get eventdetails page*/
    public function eventsDetails(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id'); // type can be event, malls and attraction
        $events = Events::select('events.*',DB::raw("CONCAT('','$url/public/upload/events/',event_image) as event_image"))->where('id',$id)->get();
        $slider = Slider::select('sliders.*',DB::raw("CONCAT('','$url/public/upload/slider/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'event'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->join('brands','products.brand_id','=','brands.id')->join('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'event'])->get();
        $photos = Photos::select('photos.*',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
         $floormap = Map_images::select('map_images.*',DB::raw("CONCAT('','$url/public/upload/map_images/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
        if($events->count() > 0)
        {
            unset($events[0]->deleted_at,$events[0]->updated_at,$floormap[0]->deleted_at,$floormap[0]->updated_at);
            if($floormap->count() > 0)
                unset($floormap[0]->deleted_at,$floormap[0]->updated_at);
            $success = $events[0];
            $success['slider'] =$slider;
            $success['brands'] =$featuredBrand;
            $success['deals'] = $deals;
            $success['photos'] =  $photos ;
            $success['floormap'] =  $floormap ;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];

           // return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /*Function Used to get malldetails page*/
    public function mallDetails(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id'); // type can be event, malls and attraction
        $malls = ShopsandMalls::select('shopsandmalls.*',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"))->where('id',$id)->get();
        $slider = Slider::select('sliders.*',DB::raw("CONCAT('','$url/public/upload/slider/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'malls'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'malls'])->get();
        $photos = Photos::select('photos.*',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        $floormap = Map_images::select('map_images.*',DB::raw("CONCAT('','$url/public/upload/map_images/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        if($malls->count() > 0)
        {
            unset($malls[0]->deleted_at,$malls[0]->updated_at);
            if($floormap->count() > 0)
                unset($floormap[0]->deleted_at,$floormap[0]->updated_at);
            
            $success = $malls[0];
            $success['slider'] =$slider;
            $success['brands'] =$featuredBrand;
            $success['deals'] = $deals;
            $success['photos'] =  $photos ;
            $success['floormap'] =  $floormap ;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];

            //return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /*Function Used to get malldetails page*/
    public function attractionDetails(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id'); // type can be event, malls and attraction
        $attraction = Attractions::select('attractions.*',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"))->where('id',$id)->get();
        $slider = Slider::select('sliders.*',DB::raw("CONCAT('','$url/public/upload/slider/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'attraction'])->get();
        $photos = Photos::select('photos.*',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $floormap = Map_images::select('map_images.*',DB::raw("CONCAT('','$url/public/upload/map_images/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        if($attraction->count() > 0)
        {
            unset($attraction[0]->deleted_at,$attraction[0]->updated_at);
            if($floormap->count() > 0)
                unset($floormap[0]->deleted_at,$floormap[0]->updated_at);
            $success = $attraction[0];
            $success['slider'] =$slider;
            $success['brands'] =$featuredBrand;
            $success['deals'] = $deals;
            $success['photos'] =  $photos ;
            $success['floormap'] =  $floormap ;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);
    }

    /* Function used to search in whole database */
    public function search(Request $request)
    {
        $url =env('APP_URL');
        $search = $request->search;
        $out = "";

        $sql = "show tables";
        $tables = DB::select('SHOW TABLES');
       
        if(count($tables) > 0){
            foreach($tables as $k=>$r)
            {
                $table = $r->Tables_in_fingertips;
                //$out .= $table.";";
                $sql_search = "select * from ".$table." where ";
                $sql_search_fields = Array();
                $sql2 = "SHOW COLUMNS FROM ".$table;
                $rs2 = DB::select($sql2);
                if(count($rs2) > 0){
                    foreach($rs2 as $r2)
                    {
                        $colum = $r2->Field;
                        $sql_search_fields[] = $colum." like('%".$search."%')";
                    }
                }
                $sql_search .= implode(" OR ", $sql_search_fields);
                $rs3 = DB::select($sql_search);

                //$out .= count($rs3)."\n";
                if(count($rs3) >0)
                {
                    if($table == 'events')
                    {
                        $rs3[0]->event_image =  $url.'public/upload/events/'.$rs3[0]->event_image;
                    }
                    if($table == 'attractions')
                    {
                        $rs3[0]->attraction_image =  $url.'public/upload/attractions/'.$rs3[0]->attraction_image;
                    }
                    if($table == 'shopsandmalls')
                    {
                        $rs3[0]->image =  $url.'public/upload/malls/'.$rs3[0]->image;
                    }
                    if($table == 'highlights')
                    {
                        $rs3[0]->image =  $url.'public/upload/highlights/'.$rs3[0]->image;
                    }
                    if($table == 'users')
                    {
                        $rs3[0]->profile_pic =  $url.'public/upload/'.$rs3[0]->profile_pic;
                    }
                    if($table == 'sliders')
                    {
                        $rs3[0]->slider_image_name =  $url.'public/upload/sliders/'.$rs3[0]->slider_image_name;
                    }
                    if($table == 'products')
                    {
                        $rs3[0]->product_image =  $url.'public/upload/products/'.$rs3[0]->product_image;
                    }
                    if($table == 'photos')
                    {
                        $rs3[0]->image_name =  $url.'public/upload/photos/'.$rs3[0]->image_name;
                    }
                    if($table == 'map_images')
                    {
                        $rs3[0]->map_image_name =  $url.'public/upload/map_images/'.$rs3[0]->map_image_name;
                    }
                    if($table == 'flash_sales')
                    {
                        $rs3[0]->map_image_name =  $url.'public/upload/flashsale/'.$rs3[0]->map_image_name;
                    }
                    if($table == 'brands')
                    {
                        $rs3[0]->brand_image =  $url.'public/upload/brands/'.$rs3[0]->brand_image;
                    }
                    if($table == 'banners')
                    {
                        $rs3[0]->bannerimage =  $url.'public/upload/banners/'.$rs3[0]->bannerimage;
                    }
                    unset($rs3[0]->deleted_at,$rs3[0]->updated_at);
                    $success[$table] = $rs3;
                }
            }
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
        }
        else
        {
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        //return $out;
        return response()->json($response);

    }

    /* Function used to insert privacy policy */
    public function privacypolicy(Request $request)
    {
        $privacy = DB::table('settings')->where('type','privacypolicy')->get();
        if($privacy->count() > 0)
        {
            unset($privacy[0]->deleted_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$privacy[0]];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);

    }

      /* Function used to insert privacy policy */
    public function termsandcondition(Request $request)
    {
        $privacy = DB::table('settings')->where('type','termsandcondition')->get();
        if($privacy->count() > 0)
        {
            unset($privacy[0]->deleted_at);
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$privacy[0]];
        }
        else
        {   
            $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
        }
        return response()->json($response);

    }




}
