<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;


class ShopsandMalls extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="shopsandmalls";

    public $sortable = ['name', 'location'];

    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    }
}
