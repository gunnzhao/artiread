<?php

namespace App\Repositries;

use App\Unread;

class UnreadRepositry
{
    /**
     * 全部站点未读文章id
     * @param  int  $userId 用户id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allArticleIds(int $userId)
    {
        return Unread::where('user_id', $userId)->pluck('article_id')->toArray();
    }

    /**
     * 一个站点的未读文章id
     * @param  int  $userId 用户id
     * @param  int $websiteId 站点id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function singleWebsiteArticleIds(int $userId, int $websiteId)
    {
        return Unread::where([
            ['user_id', $userId], ['website_id', $websiteId]
        ])->pluck('article_id')->toArray();
    }
}