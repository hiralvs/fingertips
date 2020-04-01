<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class FlashSale extends Model
{
    use SoftDeletes;
    use Sortable;
    protected $table="flash_sales";
}
