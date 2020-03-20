<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use Sortable;
    use SoftDeletes;

    public $sortable = ['name', 'product_image','price','stock','created_at'];

}
