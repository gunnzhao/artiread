<?php

namespace App\Rss;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use AthosHun\HTMLFilter\Configuration;
use AthosHun\HTMLFilter\HTMLFilter;
use App\Article;
use App\UserWebsite;
use App\Website as WebsiteModel;

class Item
{
    private $rss;

    private $data;

    public function __construct(Rss $rss, array $data)
    {
        $this->rss = $rss;
        $this->data = $data;
    }

    public function items()
    {
        return $this->data;
    }

    public function list()
    {
        if (!$this->data) {
            return [];
        }

        $config = new Configuration();
        $filter = new HTMLFilter();

        $list = [];
        foreach ($this->data as $item) {
            $list[] = [
                'title' => $item->get_title(),
                'link' => $item->get_permalink(),
                'description' => Str::limit($filter->filter($config, $item->get_content()), 300),
                'publish_time' => $item->get_date('Y年m月d日 H点i分')
            ];
        }
        return $list;
    }

    public function saveAll()
    {
        if (!$this->rss->getWebsiteId()) {
            return false;
        }

        if (!$this->data) {
            return false;
        }

        $config = new Configuration();
        $filter = new HTMLFilter();

        $updateTime = 0;
        $insertNum = 0;
        
        foreach ($this->data as $item) {
            $data = [];
            
            if (!$item->get_permalink()) {
                continue;
            }

            if (!$item->get_title()) {
                continue;
            }

            if (!$item->get_content()) {
                continue;
            }

            $data['website_id'] = $this->rss->getWebsiteId();
            $data['link'] = $item->get_permalink();
            $data['title'] = $item->get_title();
            $data['description'] = Str::limit($filter->filter($config, $item->get_content()), 300);
            $data['content'] = $item->get_content();
            $data['publish_time'] = $item->get_date('Y-m-d H:i:s');

            if (Article::where('link_md5', md5($item->get_permalink()))->first()) {
                Article::where('link_md5', md5($item->get_permalink()))->update($data);
            } else {
                $data['link_md5'] = md5($item->get_permalink());
                Article::create($data);
            }
            
            $insertNum++;

            if (strtotime($data['publish_time']) > $updateTime) {
                $updateTime = strtotime($data['publish_time']);
            }
        }

        if (!$insertNum) {
            return false;
        }

        if ($updateTime > 0) {
            if ($lastUpdateTime = $this->rss->getLastUpdateTime()) {
                $carbonTime = new Carbon($updateTime, 'Asia/Shanghai');

                if ($carbonTime->gt($lastUpdateTime)) {
                    // 如果最新的发布日期比数据库中的站点最后更新日期大，则更新数据表对应字段。
                    WebsiteModel::where('id', $this->rss->getWebsiteId())->update(['last_update_time' => $updateTime]);
                    UserWebsite::where('website_id', $this->rss->getWebsiteId())->update(['last_update_time' => $carbonTime]);
                }
            } else {
                WebsiteModel::where('id', $this->rss->getWebsiteId())->update(['last_update_time' => $updateTime]);
            }
        }

        return true;
    }
}