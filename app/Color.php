<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Color extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="colors";

    public $sortable = ['colors', 'created_at'];
}
