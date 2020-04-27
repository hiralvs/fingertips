<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Size extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="size";

    public $sortable = ['size', 'created_at'];
}
