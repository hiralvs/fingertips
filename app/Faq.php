<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Faq extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="faq";

    public $sortable = ['unique_id','title', 'created_at','description','created_by'];

}
