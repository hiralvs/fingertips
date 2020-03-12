<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class Brand extends Authenticatable
{
    use Notifiable;
    use HasApiTokens, Notifiable;
    use Sortable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_image',
        'name',
        'unique_id',
        'grand_merchant_user_id',
        'commission',
        'category_id',
        'description',
        'status',
        'created_at'
    ];

    // public $sortable = ['listing_image','unique_id','name','commission', 'status', 'created_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
