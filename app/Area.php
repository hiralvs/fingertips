<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Area extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="area";

    public $sortable = ['area_name', 'created_at'];
}
