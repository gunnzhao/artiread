<?php

namespace App\Rss;

use Illuminate\Support\Carbon;
use SimplePie;

class Rss
{
    private $feed;

    private $timeout = 5;

    private $enableCache = false;

    private $websiteId;

    private $lastUpdateTime; // 站点上次更新时间

    public $website;

    public $item;

    public function __construct(string $url)
    {
        $this->feed = new SimplePie();
        $this->feed->set_feed_url($url);
        $this->feed->set_timeout($this->timeout);
        $this->feed->enable_cache($this->enableCache);
        $this->feed->init();

        $this->website = new Website($this, $this->feed);

        if (!$this->feed->error()) {
            $this->item = new Item($this, $this->feed->get_items());
        }
    }

    public function hasError()
    {
        if ($this->feed->error()) {
            return true;
        }
        return false;
    }

    public function setWebsiteId(int $id)
    {
        $this->websiteId = $id;
    }

    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    public function setLastUpdateTime(Carbon $time)
    {
        $this->lastUpdateTime = $time;
    }

    /**
     * 获得站点最后更新时间
     * @return Illuminate\Support\Carbon
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }
}
