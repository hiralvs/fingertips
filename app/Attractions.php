<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Attractions extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="attractions";

    public $sortable = ['attraction_name', 'location'];    
}
