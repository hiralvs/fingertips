<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class ProductVariant extends Model
{
	use Sortable;
    use SoftDeletes;

    protected $table="product_variant";   
    public $sortable = ['variant_name', 'unique_id','price','stock','created_at'];

}
