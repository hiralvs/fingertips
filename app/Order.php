<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use Sortable;
    use SoftDeletes;

    public $sortable = ['order_id', 'product_id','order_bill_no','amount','created_at'];
}
