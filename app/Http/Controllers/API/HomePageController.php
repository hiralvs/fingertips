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
use App\Area;
use App\Category;
use App\Settings;

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
        
        // Filter data parameter
        $filter_data = $request->getContent();
        $eventfilter = json_decode($filter_data,true);

        $startdate = date("Y-m-d");
        if(isset($eventfilter['type']) && $eventfilter['type'] == 'Today')
        {
            $dateby = date("Y-m-d");   
        }
        else if(isset($eventfilter['type']) && $eventfilter['type'] =='This Week')
        {
            $dateby = date("Y-m-d", strtotime("+1 week"));
        }
        else if(isset($eventfilter['type']) && $eventfilter['type'] == 'This Month')
        {
            $dateby = date("Y-m-d", strtotime('+1 month'));
        }

        if(isset($request->lat) && isset($request->long))
        {
            $query = Events::select('events.id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude','event_start_date',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $request->lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $request->long . '))
                     + SIN(RADIANS(' . $request->lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'))->leftjoin('area', 'area.id', '=', 'events.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,events.category_id)"),">",DB::raw("'0'"))->groupBy("events.id");
        }
        else
        {
            $query = Events::select('events.id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude','event_start_date',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'events.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,events.category_id)"),">",DB::raw("'0'"))->groupBy("events.id");
        }
        if(isset($eventfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$eventfilter['area']);
        }
        if(isset($eventfilter['categories']))
        {
            $query = $query->whereIn('category_name',$eventfilter['categories']);
        }
        if(isset($dateby))
        {
            $query = $query->Where('event_start_date', '>=',$dateby);
        }
        $tmpquery = $query->get();
        $totalrecords = $tmpquery->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        $events = $query->offset($offset)->limit($limit)->get();

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

        $filter_data = $request->getContent();
        $shopmallfilter = json_decode($filter_data,true);

        if(isset($request->lat) && isset($request->long))
        {
            $query = ShopsandMalls::select('shopsandmalls.id','unique_id','image','name','openinghrs','closinghrs','shopsandmalls.type','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $request->lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $request->long . '))
                     + SIN(RADIANS(' . $request->lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'))->leftjoin('area', 'area.id', '=', 'shopsandmalls.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->groupBy("shopsandmalls.id");
        }
        else
        {
            $query = ShopsandMalls::select('shopsandmalls.id','unique_id','image','name','openinghrs','closinghrs','shopsandmalls.type','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'shopsandmalls.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->groupBy("shopsandmalls.id");
        }
       
        if(isset($shopmallfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$shopmallfilter['area']);
        }
        if(isset($shopmallfilter['categories']))
        {
            $query = $query->whereIn('category_name',$shopmallfilter['categories']);
        }
         if(isset($shopmallfilter['type']))
        {
            if($shopmallfilter['type'] == 'Shops')
            {
                $mtype = 'shop';
            }
            else if($shopmallfilter['type'] == 'Malls')
            {
                $mtype = 'mall';
            }

            $query = $query->where('shopsandmalls.type',$mtype);
        }

        $tmpquery = $query->get();
        $totalrecords = $tmpquery->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        $malls = $query->offset($offset)->limit($limit)->get();

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

        $filter_data = $request->getContent();
        $attractionfilter = json_decode($filter_data,true);

        if(isset($request->lat) && isset($request->long))
        {
            $lat = $request->lat;
            $long = $request->long;
            $query = Attractions::select('attractions.id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"),DB::raw('111.045 * DEGREES(ACOS(COS(RADIANS(' . $request->lat . '))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(' . $request->long . '))
                     + SIN(RADIANS(' . $request->lat . '))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km'))->leftjoin('area', 'area.id', '=', 'attractions.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,attractions.category_id)"),">",DB::raw("'0'"))->groupBy("attractions.id");
        }
        else
        {
            $query = Attractions::select('attractions.id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'attractions.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,attractions.category_id)"),">",DB::raw("'0'"))->groupBy("attractions.id");
        }       
       
        if(isset($attractionfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$attractionfilter['area']);
        }
        if(isset($attractionfilter['categories']))
        {
            $query = $query->whereIn('category_name',$attractionfilter['categories']);
        }

        $totalrecords = $query->get()->count(); 
        $totalpage = (int) ceil($totalrecords / $limit);
        $attraction = $query->offset($offset)->limit($limit)->get();
        
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
    public function highlightsnflashsale(Request $request)
    {
        $url =env('APP_URL');
        $id = $request->route('id');
        $type = $request->route('type');
        $highlights = array();
        if($type == 'event')
        {
            $highlights = Highlights::select('highlights.*','events.description','events.location','events.latitude','events.longitude',DB::raw("CONCAT('','$url/public/upload/highlights/',highlights.image) as image"))->leftjoin('events','events.id','=','highlights.common_id')->where(['common_id'=>$id,'type'=>$type])->get();

            $flashsale = FlashSale::select('flash_sales.name','flash_sales.product_id','flash_sales.discount_percentage','flash_sales.start_date','flash_sales.end_date','flash_sales.start_time','flash_sales.end_time','flash_sales.total_revenue',DB::raw("CONCAT('','$url/public/upload/flashsale/',image) as image"))->leftjoin('products','products.id','=','flash_sales.product_id')->leftjoin('brands_connection','brands_connection.brand_id','=','products.brand_id')->where(['brands_connection.common_id'=>$id,'brands_connection.type'=>$type])->groupBy('flash_sales.id')->get();
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
            $success['highlights'] = $highlights;
            $success['flashsale'] = $flashsale;
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
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
        $filter_data = $request->getContent();
        $filter = json_decode($filter_data,true);

        $success = array();
        $type = $request->type; // type can be event, malls and attraction
        $banners = Banner::select('banners.id','banners.type',DB::raw('IFNULL(banners.url , "" ) as url'),DB::raw('IFNULL(banners.ema , "" ) as ema'),DB::raw('IFNULL(banners.property_user_id , "" ) as property_user_id'),DB::raw("CONCAT('','$url/public/upload/banners/',bannerimage) as bannerimage"))->where('location',$type)->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name as productname','products.price','brands.id as bid','brands.name as brandname',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->join('brands','products.brand_id','=','brands.id')->join('brands_connection','brands_connection.brand_id','=','brands.id')->where(["brands_connection.type"=>$type])->groupBy('products.id')->get();
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
            $startdate = date("Y-m-d");
            if(isset($filter['dtype']) && $filter['dtype'] == 'Today')
            {
                $dateby = date("Y-m-d");   
            }
            else if(isset($filter['dtype']) && $filter['dtype'] =='This Week')
            {
                $dateby = date("Y-m-d", strtotime("+1 week"));
            }
            else if(isset($filter['dtype']) && $filter['dtype'] == 'This Month')
            {
                $dateby = date("Y-m-d", strtotime('+1 month'));
            }

            $featuredevents = Events::select('events.id','events.unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'events.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,events.category_id)"),">",DB::raw("'0'"))->where('featured_event','yes')->groupBy("events.id")->limit(20);
        
            if(isset($filter['area']))
            {
                $featuredevents = $featuredevents->whereIn('area.area_name',$filter['area']);
            }
            if(isset($filter['categories']))
            {
                $featuredevents = $featuredevents->whereIn('category_name',$filter['categories']);
            }
            if(isset($dateby))
            {
                $featuredevents = $featuredevents->Where('event_start_date', '>=',$dateby);
            }

            $success['featuredevents'] =$featuredevents->get();
        }
        if($type == 'malls')
        {
            $featuredShops = ShopsandMalls::select('shopsandmalls.id','shopsandmalls.unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'shopsandmalls.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->where('featured_mall','yes')->where('shopsandmalls.type','shop')->limit(20)->groupBy("shopsandmalls.id");

            $featuredmalls = ShopsandMalls::select('shopsandmalls.id','shopsandmalls.unique_id','image','name','openinghrs','closinghrs','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'shopsandmalls.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->where('featured_mall','yes')->where('shopsandmalls.type','mall')->limit(20)->groupBy("shopsandmalls.id");

            if(isset($filter['area']))
            {
                $featuredShops = $featuredShops->whereIn('area.area_name',$filter['area']);
            }
            if(isset($filter['categories']))
            {
                $featuredmalls = $featuredmalls->whereIn('category_name',$filter['categories']);
            }

            $success['featuredShops'] = $featuredShops->get();
            $success['featuredmalls'] = $featuredmalls->get();
        }
        if($type == 'attraction')
        {
           $featuredAttraction = Attractions::select('attractions.id','attractions.unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'attractions.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,attractions.category_id)"),">",DB::raw("'0'"))->groupBy("attractions.id")->where('featured_mall','yes')->groupBy("attractions.id")->limit(20);

            if(isset($filter['area']))
            {
                $featuredAttraction = $featuredAttraction->whereIn('area.area_name',$filter['area']);
            }
            if(isset($filter['categories']))
            {
                $featuredAttraction = $featuredAttraction->whereIn('category_name',$filter['categories']);
            }

            $success['featuredAttraction'] =  $featuredAttraction->get();
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
        $slider = Slider::select('sliders.id','sliders.unique_id','sliders.type','sliders.title','sliders.description','sliders.created_at',DB::raw("CONCAT('','$url/public/upload/sliders/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'event'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name as productname','products.price','brands.id as bid','brands.name as brandname',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->join('brands','products.brand_id','=','brands.id')->join('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'event'])->get();
        $photos = Photos::select('photos.id','photos.unique_id','photos.type','photos.created_at',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
        $floormap = Map_images::select('map_images.id','map_images.unique_id','map_images.type','map_images.created_at',DB::raw("CONCAT('','$url/public/upload/mall_image/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'event'])->get();
        if($events->count() > 0)
        {
            unset($events[0]->deleted_at,$events[0]->updated_at);
            // if($floormap->count() > 0)
            //     unset($floormap[0]->deleted_at,$floormap[0]->updated_at);
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
        $slider = Slider::select('sliders.id','sliders.unique_id','sliders.type','sliders.created_at','sliders.title','sliders.description',DB::raw("CONCAT('','$url/public/upload/sliders/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'malls'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name as productname','products.price','brands.id as bid','brands.name as brandname',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'malls'])->get();
        $photos = Photos::select('photos.id','photos.unique_id','photos.type','photos.created_at',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        $floormap = Map_images::select('map_images.id','map_images.unique_id','map_images.type','map_images.created_at',DB::raw("CONCAT('','$url/public/upload/mall_image/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'malls'])->get();
        if($malls->count() > 0)
        {
            unset($malls[0]->deleted_at,$malls[0]->updated_at);            
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
        $slider = Slider::select('sliders.id','sliders.unique_id','sliders.type','sliders.created_at','sliders.title','sliders.description',DB::raw("CONCAT('','$url/public/upload/sliders/',slider_image_name) as slider_image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image',DB::raw("CONCAT('','$url/public/upload/brands/',brands.brand_image) as brand_image"))->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name as productname','products.price','brands.id as bid','brands.name as brandname',DB::raw("CONCAT('','$url/public/upload/products/',products.product_image) as product_image"))->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'attraction'])->get();
        $photos = Photos::select('photos.id','photos.unique_id','photos.type','photos.created_at',DB::raw("CONCAT('','$url/public/upload/photos/',image_name) as image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $floormap = Map_images::select('map_images.id','map_images.unique_id','map_images.type','map_images.created_at',DB::raw("CONCAT('','$url/public/upload/mall_image/',map_image_name) as map_image_name"))->where(['common_id'=>$id,"type"=>'attraction'])->get();
        if($attraction->count() > 0)
        {
            unset($attraction[0]->deleted_at,$attraction[0]->updated_at);
            
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

    // /* Function used to search in whole database */
    // public function search(Request $request)
    // {
    //     $url =env('APP_URL');
    //     $search = $request->search;
    //     $out = "";

    //     $sql = "show tables";
    //     $tables = DB::select('SHOW TABLES');
    //     $success = array();
    //     if(count($tables) > 0){
    //         foreach($tables as $k=>$r)
    //         {
    //             $table = $r->Tables_in_fingertips;
    //             //$out .= $table.";";
    //             $sql_search = "select * from ".$table." where ";
    //             $sql_search_fields = Array();
    //             $sql2 = "SHOW COLUMNS FROM ".$table;
    //             $rs2 = DB::select($sql2);
    //             if(count($rs2) > 0){
    //                 foreach($rs2 as $r2)
    //                 {
    //                     $colum = $r2->Field;
    //                     $sql_search_fields[] = $colum." like('%".$search."%')";
    //                 }
    //             }
    //             $sql_search .= implode(" OR ", $sql_search_fields);
    //             $rs3 = DB::select($sql_search);

    //             //$out .= count($rs3)."\n";
    //             //echo "<pre>"; print_r($rs3);
    //             if(count($rs3) >0)
    //             {	
    //             	foreach ($rs3 as $key => $finvalue) 
    //             	{
	   //              	if($table == 'events')
	   //                  {
	   //                      $finvalue->event_image =  $url.'/public/upload/events/'.$finvalue->event_image;
	   //                  }
	   //                  if($table == 'attractions')
	   //                  {
	   //                      $finvalue->attraction_image =  $url.'/public/upload/attractions/'.$finvalue->attraction_image;
	   //                  }
	   //                  if($table == 'shopsandmalls')
	   //                  {
	   //                      $finvalue->image =  $url.'/public/upload/malls/'.$finvalue->image;
	   //                  }
	   //                  if($table == 'highlights')
	   //                  {
	   //                      $finvalue->image =  $url.'/public/upload/highlights/'.$finvalue->image;
	   //                  }
	   //                  if($table == 'users')
	   //                  {
	   //                      $finvalue->profile_pic =  $url.'/public/upload/'.$finvalue->profile_pic;
	   //                  }
	   //                  if($table == 'sliders')
	   //                  {
	   //                      $finvalue->slider_image_name =  $url.'/public/upload/sliders/'.$finvalue->slider_image_name;
	   //                  }
	   //                  if($table == 'products')
	   //                  {
	   //                      $finvalue->product_image =  $url.'/public/upload/products/'.$finvalue->product_image;
	   //                  }
	   //                  if($table == 'photos')
	   //                  {
	   //                      $finvalue->image_name =  $url.'/public/upload/photos/'.$finvalue->image_name;
	   //                  }
	   //                  if($table == 'map_images')
	   //                  {
	   //                      $finvalue->map_image_name =  $url.'/public/upload/mall_image/'.$finvalue->map_image_name;
	   //                  }
	   //                  if($table == 'flash_sales')
	   //                  {
	   //                      $finvalue->image =  $url.'/public/upload/flashsale/'.$finvalue->image;
	   //                  }
	   //                  if($table == 'brands')
	   //                  {
	   //                      $finvalue->brand_image =  $url.'/public/upload/brands/'.$finvalue->brand_image;
	   //                  }
	   //                  if($table == 'banners')
	   //                  {
	   //                      $finvalue->bannerimage =  $url.'/public/upload/banners/'.$finvalue->bannerimage;
	   //                  }
    //                 	unset($finvalue->deleted_at,$finvalue->updated_at);
    //             		$success[$table][] = $finvalue;
    //             	}
                   
    //                 //$success[$table] = $rs3;
    //                 $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
    //             }
    //             else
	   //          {
	   //          	$response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>$success];
	   //          }
                
    //         }
            
            
    //     }
    //     else
    //     {
    //         $response = ['success' => false,'status'=> 404,'message' => 'No Data Found']; 
    //     }
    //     //return $out;
    //     return response()->json($response);

    // }

    /* Function used to search in whole database */
    public function search(Request $request)
    {
        $url =env('APP_URL');
        $search = $request->search;
        $out = "";

        $tables = ['events','attractions','shopsandmalls','products','brands'];
        $success = array();

        foreach ($tables as $key => $tables) 
        {
               // echo $sql_search = "select *, GROUP_CONCAT(category_name ORDER BY category.id) as category_name from ".$tables." leftjoin category ON FIND_IN_SET(category.id, ".$tables.".category_id) > 0 GROUP BY ".$tables.".id where ";
                $sql_search = DB::table($tables)
                     ->select($tables.'.*',DB::raw("GROUP_CONCAT(category_name) as category_name"))
                     ->leftjoin('category', DB::raw("FIND_IN_SET(category.id,".$tables.".category_id)"),">",DB::raw("'0'"))
                     ->groupBy($tables.'.id');

                $sql_search_fields = Array();
                $sql2 = "SHOW COLUMNS FROM ".$tables;
                $rs2 = DB::select($sql2);
                if(count($rs2) > 0){
                    $sql_search = $sql_search->where('category_name','LIKE',"%{$search}%");
                    foreach($rs2 as $r2)
                    {
                        $colum = $r2->Field;
                        $sql_search = $sql_search->orWhere($tables.'.'.$colum,'LIKE',"%{$search}%");
                        //$sql_search_fields[] = $colum." like('%".$search."%')";
                    }
                }
                //$sql_search .= implode(" OR ", $sql_search_fields);
                $rs3 = $sql_search->get();
                //$out .= count($rs3)."\n";
                //echo "<pre>"; print_r($rs3);
                if($rs3->count() >0)
                {   
                    foreach ($rs3 as $key => $finvalue) 
                    {
                        if($tables == 'events')
                        {
                            $finvalue->event_image =  $url.'/public/upload/events/'.$finvalue->event_image;
                        }
                        if($tables == 'attractions')
                        {
                            $finvalue->attraction_image =  $url.'/public/upload/attractions/'.$finvalue->attraction_image;
                        }
                        if($tables == 'shopsandmalls')
                        {
                            $finvalue->image =  $url.'/public/upload/malls/'.$finvalue->image;
                        }
                        if($tables == 'products')
                        {
                            $finvalue->product_image =  $url.'/public/upload/products/'.$finvalue->product_image;
                        }
                       
                        unset($finvalue->deleted_at,$finvalue->updated_at);
                        $success[$tables][] = $finvalue;
                    }
                   
                    //$success[$table] = $rs3;
                    $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];
                }
                else
                {
                    $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>$success];
                }
                
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

    /* Function used to get nearby ESMA*/
    public function nearbyESMA(Request $request)
    {
        $lat = $request->lat;
        $long = $request->long;

        $tables = ['events','attractions','shopsandmalls'];
        $settings = Settings::where('type','nearme')->first();
        foreach ($tables as $key => $tables) {
           // $sql_search = "SELECT latitude,longitude, (((acos(sin((".$lat." * pi() / 180)) * sin(( `latitude` * pi() / 180)) + cos((".$lat." * pi() /180 )) * cos(( `latitude` * pi() / 180)) * cos(((".$long." - `longitude`) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) AS distance_in_km FROM ".$tables." ORDER BY distance_in_km ASC";
            $sql_search = "SELECT ".$tables.".*,latitude,longitude,111.045 * DEGREES(ACOS(COS(RADIANS(".$lat."))
                     * COS(RADIANS(latitude))
                     * COS(RADIANS(longitude) - RADIANS(".$long."))
                     + SIN(RADIANS(".$lat."))
                     * SIN(RADIANS(latitude))))
                     AS distance_in_km FROM ".$tables." HAVING distance_in_km <10000 ORDER BY distance_in_km ASC";
            $rs3 = DB::select($sql_search);
            $success[$tables] = $rs3;            
        }
        
        if($success)
        {
            $settings->value = $settings->value+1; 
            $settings->save();
            $response = ['success' => true,'status' => 200,'message' => 'Data Found Successfully.','data'=>$success];    
        }
        else
        {
            $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>[]];
        }
        return response()->json($response);

    }

     /* Function used to filter event*/
    public function eventFilter(Request $request)
    {
        $url =env('APP_URL'); 

        $filter_data = $request->getContent();
        $eventfilter = json_decode($filter_data,true);

        $startdate = date("Y-m-d");
        if($eventfilter['type'] == 'today')
        {
            $dateby = date("Y-m-d");   
        }
        else if($eventfilter['type'] =='week')
        {
            $dateby = date("Y-m-d", strtotime("+1 week"));
        }
        else if($eventfilter['type'] == 'month')
        {
            $dateby = date("Y-m-d", strtotime('+1 month'));
        }

        $query = Events::select('events.id','unique_id','event_image','event_name','start_time','end_time','location','latitude','longitude','event_start_date',DB::raw("CONCAT('','$url/public/upload/events/',events.event_image) as event_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'events.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,events.category_id)"),">",DB::raw("'0'"))->groupBy("events.id");
        if(isset($eventfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$eventfilter['area']);
        }
        if(isset($eventfilter['categories']))
        {
            $query = $query->whereIn('category_name',$eventfilter['categories']);
        }
        if(isset($dateby))
        {
            $query = $query->Where('event_start_date', '>=',$dateby);
        }

        $events = $query->get();
        if($events->count() >0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found Successfully.','data'=>$events];    
        }
        else
        {
            $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>[]];
        }
        return response()->json($response);
    }


     /* Function used to filter attraction*/
    public function attractionFilter(Request $request)
    {
        $url =env('APP_URL'); 

        $filter_data = $request->getContent();
        $attractionfilter = json_decode($filter_data,true);

        $query = Attractions::select('attractions.id','unique_id','attraction_image','attraction_name','opening_time','closing_time','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/attractions/',attraction_image) as attraction_image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'attractions.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,attractions.category_id)"),">",DB::raw("'0'"))->groupBy("attractions.id");
       
        if(isset($attractionfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$attractionfilter['area']);
        }
        if(isset($attractionfilter['categories']))
        {
            $query = $query->whereIn('category_name',$attractionfilter['categories']);
        }

        $attractions = $query->get();
        if($attractions->count() >0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found Successfully.','data'=>$attractions];    
        }
        else
        {
            $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>[]];
        }
        return response()->json($response);
    }


     /* Function used to filter shopmall*/
    public function shopmallFilter(Request $request)
    {
        $url =env('APP_URL'); 

        $filter_data = $request->getContent();
        $shopmallfilter = json_decode($filter_data,true);

        $query = ShopsandMalls::select('shopsandmalls.id','unique_id','image','name','openinghrs','closinghrs','shopsandmalls.type','location','latitude','longitude',DB::raw("CONCAT('','$url/public/upload/malls/',image) as image"),'area.area_name as area_name',DB::raw("GROUP_CONCAT(category_name) as category_name"))->leftjoin('area', 'area.id', '=', 'shopsandmalls.area_id')->leftjoin('category', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->groupBy("shopsandmalls.id");
       
        if(isset($shopmallfilter['area']))
        {
            $query = $query->whereIn('area.area_name',$shopmallfilter['area']);
        }
        if(isset($shopmallfilter['categories']))
        {
            $query = $query->whereIn('category_name',$shopmallfilter['categories']);
        }
         if(isset($shopmallfilter['type']))
        {
            $query = $query->whereIn('shopsandmalls.type',$shopmallfilter['type']);
        }

        $shopmall = $query->get();
        if($shopmall->count() >0)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found Successfully.','data'=>$shopmall];    
        }
        else
        {
            $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>[]];
        }
        return response()->json($response);
    }

    public function filters(Request $request)
    {
        $type = $request->type;
        if($type == 'event')
        {
            $events = ['Today'=>'Today','This Week'=>'This Week','This Month'=>'This Month'];
            $area = Area::select('area.id','area_name')->join('events', 'area.id', '=', 'events.area_id')->groupby('area.id')->get();
            $category = Category::select('category.id','category_name')->join('events', DB::raw("FIND_IN_SET(category.id,events.category_id)"),">",DB::raw("'0'"))->groupby('category.id')->get();
            $success['other'] = $events;
        }
        else if($type == 'malls')
        {
            $area = Area::select('area.id','area_name')->join('shopsandmalls', 'area.id', '=', 'shopsandmalls.area_id')->groupby('area.id')->get();
            $category = Category::select('category.id','category_name')->join('shopsandmalls', DB::raw("FIND_IN_SET(category.id,shopsandmalls.category_id)"),">",DB::raw("'0'"))->groupby('category.id')->get();
             $success['other'] = ['Shops'=>'Shops','Malls'=>'Malls'];
        }
        else if($type == 'attraction')
        {
            $area = Area::select('area.id','area_name')->join('attractions', 'area.id', '=', 'attractions.area_id')->groupby('area.id')->get();
            $category = Category::select('category.id','category_name')->join('attractions', DB::raw("FIND_IN_SET(category.id,attractions.category_id)"),">",DB::raw("'0'"))->groupby('category.id')->get();
        }

        $success['area'] = $area;
        $success['category'] = $category;
        if($success)
        {
            $response = ['success' => true,'status' => 200,'message' => 'Data Found successfully.','data'=>$success];    
        }
        else
        {
            $response = ['success' => false,'status' => 404,'message' => 'Data Not Found.','data'=>[]];
        }
        
        return response()->json($response);

    }


}
