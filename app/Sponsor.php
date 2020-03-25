<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;


class Sponsor extends Model
{
     use SoftDeletes;
    use Sortable;
    protected $table="sponsors";
}
