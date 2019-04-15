<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWebsite extends Model
{
    public $timestamps = false;
    
    protected $table = 'user_website';

    protected $fillable = [
        'last_unread_check_time', 'last_update_time',
    ];

    public function websites()
    {
        return $this->belongsTo('App\Website', 'website_id');
    }
}
