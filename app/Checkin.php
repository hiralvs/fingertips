<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Checkin extends Model
{
     use Sortable;
    use SoftDeletes;
    protected $table="checkin";
}
