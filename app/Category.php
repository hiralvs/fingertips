<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="category";

    public $sortable = ['category_name', 'created_at'];
}
