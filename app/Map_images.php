<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Map_images extends Model
{
    use SoftDeletes;
    use Sortable;
    protected $table="map_images";
    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    }
}
