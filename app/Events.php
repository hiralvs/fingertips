<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Events extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="events";

    public $sortable = ['event_name', 'location'];
    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    }
}
