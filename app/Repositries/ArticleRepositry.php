<?php

namespace App\Repositries;

use Carbon\Carbon;
use App\Article;

class ArticleRepositry
{
    /**
     * 站点近10篇文章
     * @param  int $websiteId 站点id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function recentArticles(int $websiteId)
    {
        return Article::where([
            ['website_id', $websiteId], ['status', 0]
        ])->latest('publish_time')->limit(10)->get();
    }

    /**
     * 最新文章
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function news(int $offset = 0, int $limit = 20)
    {
        return Article::where([
            ['home_display', 1], ['status', 0]
        ])->latest('publish_time')->offset($offset)->limit($limit)->get();
    }

    /**
     * 近7天点击量高的文章
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function hots(int $offset = 0, int $limit = 20)
    {
        return Article::where([
            ['publish_time', '>', Carbon::today()],
            ['home_display', 1],
            ['status', 0]
        ])->latest('click')->offset($offset)->limit($limit)->get();
    }

    /**
     * 部分站点的最新文章
     * @param  array $websiteIds 站点id数组
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function followNews(array $websiteIds, int $offset = 0, int $limit = 10)
    {
        if (count($websiteIds) > 1) {
            return Article::whereIn('website_id', $websiteIds)->where('status', 0)
                ->latest('publish_time')->offset($offset)->limit($limit)->get();
        } else {
            return Article::where([
                ['website_id', $websiteIds[0]], ['status', 0]
            ])->latest('publish_time')->offset($offset)->limit($limit)->get();
        }
    }

    /**
     * 通过id获取部分文章
     * @param  array $articleIds 文章id
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function someArticles(array $articleIds, int $offset = 0, int $limit = 10)
    {
        if (count($articleIds) > 1) {
            return Article::whereIn('id', $articleIds)->latest('publish_time')->offset($offset)->limit($limit)->get();
        } else {
            return Article::where('id', $articleIds[0])->latest('publish_time')->offset($offset)->limit($limit)->get();
        }
    }

    /**
     * 在标题中搜索包含关键词的文章
     * @param  int $websiteId 站点id
     * @param  string $keywords 关键词
     * @param  int  $offset 查询起始游标
     * @param  int  $limit 查询量
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function titleSearch(int $websiteId, string $keywords, int $offset = 0, int $limit = 10)
    {
        return Article::where([
            ['website_id', $websiteId],
            ['title', 'like', '%' . $keywords . '%'],
            ['status', 0]
        ])->latest('publish_time')->offset($offset)->limit($limit)->get();
    }
}
