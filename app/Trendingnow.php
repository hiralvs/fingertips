<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Trendingnow extends Model
{
    use SoftDeletes;
    use Sortable;
    protected $table="trending_now";
}
