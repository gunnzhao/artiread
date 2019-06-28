<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'host', 'scheme', 'name', 'logo', 'description', 'followers', 'last_update_time',
        'home_display', 'status',
    ];

    protected $dates = [
        'last_update_time',
    ];
}
