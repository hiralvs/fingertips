<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Highlights extends Model
{
    use SoftDeletes;
    use Sortable;
    protected $table="highlights";
    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    }
}
