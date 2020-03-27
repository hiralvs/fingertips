<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Slider extends Model
{
    use Sortable;
    use SoftDeletes;

    public function notHavingImageInDb()
    {
        return (empty($this->image))?true:false;
    }
}
