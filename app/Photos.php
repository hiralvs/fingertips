<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Photos extends Model
{
    use SoftDeletes;
    use Sortable;
    protected $table="photos";   
    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    } 
}
