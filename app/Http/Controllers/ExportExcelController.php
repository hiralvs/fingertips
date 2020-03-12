<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;


class ExportExcelController extends Controller
{
    function index(){
        $customer_data = DB::table('brands')->get();
        return view('export_excel')->with('brand_data', $customer_data);

    }
    function excel(){
        $customer_data = DB::table('brands')->get()->toArray();
        $customer_array[] = array('Id','Name','No Of Products', 'Category', 'No Of Presence', 'Total Earnings');
        foreach($customer_data as $customer)
        {
            $customer_array[] = array(
                'Id'                => $customer->unique_id,
                'Name'              => $customer->name,
                // 'No Of Products'    => $customer->NoOfProducts,
                'Category'          => $customer->category_id,
                // 'No Of Presence'    => $customer->NoOfPresence,
                'Total Earnings'    => $customer->commission
            );
        }
        // Excel::store('Brand Data', function($excel) use ($customer_array){
        //     $excel->setTitle('Customer Data');
        //     $excel->setTitle('Customer Data', function($sheet) use ($customer_array){
        //         $sheet->formArray($customer_array, null, 'A1',false,false);
        //     });
        // })->download('xlsx');
        Excel::store($customer_array);
        Excel::download/Excel::store($customer_array);
        Excel::raw($customer_array, Excel::XLSX);

    }
}