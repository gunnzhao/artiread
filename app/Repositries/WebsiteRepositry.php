<?php

namespace App\Repositries;

use Carbon\Carbon;
use App\Website;

class WebsiteRepositry
{
    /**
     * 最新更新的站点
     */
    public function getActiveWebsite(int $offset = 0, int $limit = 10)
    {
        return Website::where('status', 0)->latest('last_update_time')->offset($offset)->limit($limit)->get();
    }

    /**
     * 近30天关注量高的站点
     */
    public function highFollowerWebsite()
    {
        return Website::where([
            ['status', 0], ['last_update_time', '>', Carbon::now()->subDays(30)]
        ])->latest('followers')->limit(10)->get();
    }
    
    public function getById($id)
    {
        return Website::where([
            ['id', $id], ['status', 0]
        ])->first();
    }

    /**
     * 通过url获取一条记录
     * @param  string $url 站点url
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getByUrl($url)
    {
        return Website::where([
            ['url', $url], ['status', 0]
        ])->first();
    }

    /**
     * 通过主机地址获取一条记录
     * @param  string $host 主机地址
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getByHost($host)
    {
        return Website::where([
            ['host', $host], ['status', 0]
        ])->first();
    }

    /**
     * 通过主机地址模糊匹配一条记录
     * @param  string $domain 主机地址
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLikeHost($host, int $offset = 0, int $limit = 10)
    {
        return Website::where([
            ['host', 'like', '%' . $host . '%'], ['status', 0]
        ])->latest('followers')->offset($offset)->limit($limit)->get();
    }

    /**
     * 通过名称获取一条记录
     * @param  string $name 站点名称
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getByName(string $name)
    {
        return Website::where([
            ['name', $name], ['status', 0]
        ])->first();
    }

    /**
     * 通过名称模糊匹配记录
     * @param  string $name 站点名称
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getLikeName(string $name, int $offset = 0, int $limit = 10)
    {
        return Website::where([
            ['name', 'like', '%' . $name . '%'], ['status', 0]
        ])->latest('followers')->offset($offset)->limit($limit)->get();
    }
}
