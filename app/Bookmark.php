<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id', 'website_id', 'article_id',
    ];

    public function articles()
    {
        return $this->belongsTo('App\Article', 'article_id');
    }
}
