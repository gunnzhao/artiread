<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unread extends Model
{
    protected $fillable = [
        'user_id', 'website_id', 'article_id',
    ];
}
