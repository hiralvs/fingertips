<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;


class Banner extends Model
{
    use Sortable;
    use SoftDeletes;

    public $sortable = ['location', 'bannerimage','type','url','ema','property_user_id', 'created_at'];

}
