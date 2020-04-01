<?php

namespace App\Exports;
 
use App\Brand;
use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
 
class BrandsExport implements FromQuery
{
    public function query()
    {
        return Brand::select('id','name')->where('id', '>', 25);
        // return Brand::get();
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            Date::dateTimeExcel($invoice->create_at),
        ];
    }
}


// THIS IS THE WORKING CODE.......

// <?php

// namespace App\Exports;
 
// use App\Brand;
// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\FromCollection;
 
// class BrandsExport implements FromCollection
// {
//     public function collection()
//     {
//         return Brand::get();
//     }
// }
