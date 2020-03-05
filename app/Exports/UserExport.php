<?php

namespace App\Exports;
 
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
 
class UserExport implements FromCollection
{
    public function collection()
    {
        return User::get();
    }
}
