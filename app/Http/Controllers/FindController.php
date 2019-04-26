<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Auth;
use App\Repositries\WebsiteRepositry;
use App\Rss\Rss;

class FindController extends Controller
{
    protected $websiteRepositry;

    /**
     * 当前页数
     */
    protected $nowPage = 1;

    /**
     * 每页记录数量
     */
    protected $pageNum = 10;

    /**
     * 当前页面url
     */
    protected $link = '/find';

    /**
     * 搜索关键词
     */
    protected $keywords = '';

    /**
     * 查询结果集
     */
    protected $results;

    public function __construct(WebsiteRepositry $websiteRepositry)
    {
        $this->websiteRepositry = $websiteRepositry;

        $this->results = new Collection();
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'p' => 'int|min:1',
            'q' => 'string|max:255',
        ]);

        $this->nowPage = $request->has('p') ? $request->input('p') : 1;
        $this->keywords = $request->has('q') ? $request->input('q') : '';

        if (!$this->keywords) {
            $offset = ($this->nowPage - 1) * $this->pageNum;

            $this->results = $this->websiteRepositry->getActiveWebsite($offset, $this->pageNum + 1);
            return view('find/index', $this->genVal());
        } else {
            $parser = parse_url($this->keywords);

            if ($parser and isset($parser['host'])) {
                // URL地址查找
                return $this->searchByUrl($this->keywords);
            } elseif ($this->checkChinese($this->keywords)) {
                // 含有中文，说明在通过名称查找
                return $this->searchByName($this->keywords);
            } elseif (strpos($this->keywords, '.') !== false) {
                // 含有“.”有可能是host
                return $this->searchByHost($this->keywords);
            } else {
                // 非以上三种情况都认为是在通过名称查找
                return $this->searchByName($this->keywords);
            }
        }
    }

    protected function searchByUrl(string $url)
    {
        $feed = new Rss($url);
        if ($feed->hasError()) {
            return view('find/index', $this->genVal());
        }

        $website = $this->websiteRepositry->getByUrl($url);
        if ($website) {
            $feed->setWebsiteId($website->id);
            $feed->setLastUpdateTime($website->last_update_time);
            $feed->item->saveAll();
            return redirect('/website/' . $website->id);
        }

        if (!Auth::check()) {
            return view('find/index', $this->genVal());
        }

        $websiteId = $feed->website->save();
        if (!$websiteId) {
            return view('find/index', $this->genVal());
        }

        $feed->item->saveAll();
        return redirect('/website/' . $websiteId);
    }

    protected function searchByHost(string $host)
    {
        $offset = ($this->nowPage - 1) * $this->pageNum;

        // 通过host检查数据库中是否存在
        $this->results = $this->websiteRepositry->getLikeHost($host, $offset, $this->pageNum + 1);
        if ($this->results->count() == 0) {
            // host不存在再通过名称查找
            $this->results = $this->websiteRepositry->getLikeName($host, $offset, $this->pageNum + 1);
        }
        return view('find/index', $this->genVal());
    }

    protected function searchByName(string $name)
    {
        if ($this->checkChinese($name)) {
            // 中文名称至少有2个
            if (mb_strlen($name, 'utf-8') < 2) {
                return view('find/index', $this->genVal());
            }
        } else {
            // 英文名称至少有4个
            if (mb_strlen($name, 'utf-8') < 4) {
                return view('find/index', $this->genVal());
            }
        }

        $offset = ($this->nowPage - 1) * $this->pageNum;
        $this->results = $this->websiteRepositry->getLikeName($name, $offset, $this->pageNum + 1);
        return view('find/index', $this->genVal());
    }

    protected function genVal()
    {
        $urlParams = [];

        if ($this->keywords != '') {
            $urlParams[] = 'q=' . $this->keywords;
        }
        
        return [
            'nowPage' => $this->nowPage,
            'overagePage' => ceil($this->results->count() / $this->pageNum),
            'pageNum' => $this->pageNum,
            'link' => $this->link . ($urlParams ? '?' . $urlParams[0] . '&' : '?'),
            'keywords' => $this->keywords,
            'results' => $this->results,
        ];
    }

    /**
     * 检查字符串中是否含有中文
     * @param  string $str 需要检查的字符串
     * @return bool true or false
     */
    private function checkChinese(string $str)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str) > 0) {
            // 完全中文
            return true;
        } elseif (preg_match('/[\x{4e00}-\x{9fa5}]/u', $str) > 0) {
            // 含有中文
            return true;
        } else {
            // 没有中文
            return false;
        }
    }
}
