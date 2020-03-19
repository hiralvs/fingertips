<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Notification extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="notifications";

    public $sortable = ['title', 'description'];    
}
 