<?php

namespace App\Rss;

use Illuminate\Support\Carbon;
use SimplePie;

class Rss
{
    private $feed;

    private $timeout = 5;

    private $enableCache = false;

    public $websiteModel;

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

    public function setWebsiteModel($website)
    {
        $this->websiteModel = $website;
    }
}
