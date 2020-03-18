<?php

namespace App;
use Kyslik\ColumnSortable\Sortable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
        use Sortable;
    use SoftDeletes;
        public $sortable = ['title','value'];


}
