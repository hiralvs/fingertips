<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomePageController extends BaseController
{
    
    /*Function Used to get events*/
    public function eventListing(Request $request)
    {
        $events = Events::select('id','unique_id','event_image','event_name','start_time','end_time')->get();
        if($events->count() > 0)
        {
            $success['data'] = $events;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get featured events*/
    public function featuredEvents(Request $request)
    {
        $featuredevents = Events::select('id','unique_id','event_image','event_name','start_time','end_time')->where('featured_event','yes')->limit(20)->get();
        if($featuredevents->count() > 0)
        {
            $success['data'] = $featuredevents;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get featured shops*/
    public function featuredShops(Request $request)
    {
        $featuredShops = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs')->where('featured_mall','yes')->where('type','shop')->limit(20)->get();
        if($featuredShops->count() > 0)
        {
            $success['data'] = $featuredShops;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

      /*Function Used to get featured malls*/
      public function featuredMalls(Request $request)
      {
          $featuredmalls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs')->where('featured_mall','yes')->where('type','mall')->limit(20)->get();
          if($featuredmalls->count() > 0)
          {
              $success['data'] = $featuredmalls;
              return $this->sendResponse($success, 'Data Found successfully.');
          }
          else
          {   
              return $this->sendError('No Data.', ['error'=>'No Data Found']);
          }
      }

        /*Function Used to get malls*/
    public function mallListing(Request $request)
    {
        $malls = ShopsandMalls::select('id','unique_id','image','name','openinghrs','closinghrs')->get();
        if($malls->count() > 0)
        {
            $success['data'] = $malls;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

   /*Function Used to get attraction*/
    public function attractionListing(Request $request)
    {
        $attraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time')->get();
        if($attraction->count() > 0)
        {
            $success['data'] = $attraction;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get featured attraction*/
    public function featuredAttraction(Request $request)
    {
        $featuredAttraction = Attractions::select('id','unique_id','attraction_image','attraction_name','opening_time','closing_time')->where('featured_mall','yes')->limit(20)->get();
        if($featuredAttraction->count() > 0)
        {
            $success['data'] = $featuredAttraction;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get featrued attraction*/
    public function highlights(Request $request)
    {
        $id = $request->route('id');
        $type = $request->route('type');
        $highlights = array();
        if($type == 'event')
        {
            $highlights = Highlights::select('highlights.*','events.description')->leftjoin('events','events.id','=','highlights.common_id')->where(['common_id'=>$id,'type'=>$type])->get();
        }
        if($type == 'malls')
        {
            $highlights = Highlights::select('highlights.*','shopsandmalls.description')->leftjoin('shopsandmalls','shopsandmalls.id','=','highlights.common_id')->where(['common_id'=>$id,'highlights.type'=>$type])->get();
        }
        if($type == 'attraction')
        {
            $highlights = Highlights::select('highlights.*','attractions.description')->leftjoin('attractions','attractions.id','=','highlights.common_id')->where(['common_id'=>$id,'highlights.type'=>$type])->get();
        }
        
        if($highlights->count() > 0)
        {
            $success['data'] = $highlights;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get featrued attraction*/
    public function flashsale(Request $request)
    {
        $flashsale = FlashSale::all();
        if($flashsale->count() > 0)
        {
            $success['data'] = $flashsale;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get banner image of each tab*/
    public function banners(Request $request)
    {
        $type = $request->route('type'); // type can be event, malls and attraction
        $banners = Banner::where('location',$type)->get();
        if($banners->count() > 0)
        {
            $success['data'] = $banners;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get eventdetails page*/
    public function eventsDetails(Request $request)
    {
        $id = $request->route('id'); // type can be event, malls and attraction
        $events = Events::where('id',$id)->get();
        $slider = Slider::where(['common_id'=>$id,"type"=>'event'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'event'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name')->join('brands','products.brand_id','=','brands.id')->join('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'event'])->get();
        $photos = Photos::where(['common_id'=>$id,"type"=>'event'])->get();
        $floormap = Map_images::where(['common_id'=>$id,"type"=>'event'])->get();
        if($events->count() > 0)
        {
            $success['eventData'] = $events;
            $success['eventData']['slider'] =$slider;
            $success['eventData']['brands'] =$featuredBrand;
            $success['eventData']['deals'] = $deals;
            $success['eventData']['photos'] =  $photos ;
            $success['eventData']['floormap'] =  $floormap ;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get malldetails page*/
    public function mallDetails(Request $request)
    {
        $id = $request->route('id'); // type can be event, malls and attraction
        $malls = ShopsandMalls::where('id',$id)->get();
        $slider = Slider::where(['common_id'=>$id,"type"=>'malls'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'malls'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name')->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'malls'])->get();
        $photos = Photos::where(['common_id'=>$id,"type"=>'malls'])->get();
        $floormap = Map_images::where(['common_id'=>$id,"type"=>'malls'])->get();
        if($malls->count() > 0)
        {
            $success['mallData'] = $malls;
            $success['mallData']['slider'] =$slider;
            $success['mallData']['brands'] =$featuredBrand;
            $success['mallData']['deals'] = $deals;
            $success['mallData']['photos'] =  $photos ;
            $success['mallData']['floormap'] =  $floormap ;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }

    /*Function Used to get malldetails page*/
    public function attractionDetails(Request $request)
    {
        $id = $request->route('id'); // type can be event, malls and attraction
        $malls = Attractions::where('id',$id)->get();
        $slider = Slider::where(['common_id'=>$id,"type"=>'attraction'])->get();
        $featuredBrand = Brand::select('brands.id as bid','brands.name','brands.brand_image')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"type"=>'attraction'])->get();
        $deals = Product::select('products.id as pid','products.unique_id as puniqueid','products.product_image','products.name','products.price','brands.id as bid','brands.name')->leftjoin('brands','products.brand_id','=','brands.id')->leftjoin('brands_connection','brands_connection.brand_id','=','brands.id')->where(['common_id'=>$id,"brands_connection.type"=>'attraction'])->get();
        $photos = Photos::where(['common_id'=>$id,"type"=>'attraction'])->get();
        $floormap = Map_images::where(['common_id'=>$id,"type"=>'attraction'])->get();
        if($malls->count() > 0)
        {
            $success['mallData'] = $malls;
            $success['mallData']['slider'] =$slider;
            $success['mallData']['brands'] =$featuredBrand;
            $success['mallData']['deals'] = $deals;
            $success['mallData']['photos'] =  $photos ;
            $success['mallData']['floormap'] =  $floormap ;
            return $this->sendResponse($success, 'Data Found successfully.');
        }
        else
        {   
            return $this->sendError('No Data.', ['error'=>'No Data Found']);
        }
    }


}
