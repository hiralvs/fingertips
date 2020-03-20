<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;


class Help extends Model
{
    use Sortable;
    use SoftDeletes;
    protected $table="help";
    public $sortable = ['name', 'address'];
}