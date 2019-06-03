<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'website_id', 'link_md5', 'link', 'title', 'cover_pic', 'mark',
        'description', 'content', 'publish_time', 'status',
    ];

    protected $dates = [
        'publish_time',
    ];

    public function website()
    {
        return $this->belongsTo('App\Website', 'website_id');
    }

    public function bookmark()
    {
        return $this->hasMany('App\Bookmark');
    }
}
