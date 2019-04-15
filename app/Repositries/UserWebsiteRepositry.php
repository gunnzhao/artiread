<?php

namespace App\Repositries;

use App\UserWebsite;

class UserWebsiteRepositry
{
    /**
     * 订阅的站点id
     * @param  int $userId 用户id
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return array
     */
    public function followWebsiteIds(int $userId, int $offset = 0, int $limit = 30)
    {
        return UserWebsite::where('user_id', $userId)->latest('id')
            ->offset($offset)->limit($limit)->pluck('website_id')->toArray();
    }
}