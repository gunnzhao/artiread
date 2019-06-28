<?php

namespace App\Rss;

use Illuminate\Support\Str;
use SimplePie;
use App\Website as WebsiteModel;

class Website
{
    private $rss;

    private $feed;

    private $info = [];

    public function __construct(Rss $rss, SimplePie $feed)
    {
        $this->rss = $rss;
        $this->feed = $feed;
        $this->setInfo();
    }

    protected function setInfo()
    {
        if ($this->feed->error()) {
            return false;
        }

        $this->info = [
            'url' => $this->feed->subscribe_url(),
            'title' => $this->feed->get_title(),
            'logo_title' => $this->feed->get_image_title() ? $this->feed->get_image_title() : '',
            'description' =>$this->feed->get_description(),
            'item_quantity' => $this->feed->get_item_quantity(), // 文章数量
        ];

        if ($this->feed->get_image_url()) {
            $this->info['logo_url'] = $this->feed->get_image_url();
        } elseif ($this->feed->get_image_link()) {
            $this->info['logo_url'] = $this->feed->get_image_link();
        } else {
            $this->info['logo_url'] = '';
        }
    }

    /**
     * 获取站点信息
     * @return array
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * 保存站点信息
     * @param  array $websiteInfo 站点信息
     * @return int|bool 失败返回false，成功返回自增id
     */
    public function save()
    {
        if (!$this->info) {
            return false;
        }

        $parser = parse_url($this->info['url']);
        if (!isset($parser['host'])) {
            return false;
        }

        if (!isset($this->info['title']) or empty($this->info['title'])) {
            return false;
        }

        $data = [
            'url' => $this->info['url'],
            'host' => $parser['host'],
            'scheme' => $parser['scheme'],
            'name' => $this->info['title'],
            'logo' => $this->info['logo_url'],
            'description' => Str::limit($this->info['description'], 300),
            'home_display' => 1
        ];

        $website = WebsiteModel::create($data);
        if (!$website) {
            return false;
        }

        $this->rss->setWebsiteModel($website);

        return $website->id;
    }
}