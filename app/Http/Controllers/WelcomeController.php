<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Repositries\WebsiteRepositry;
use App\Repositries\ArticleRepositry;
use App\Rss\Rss;

class WelcomeController extends Controller
{
    /**
     * 当前页数
     */
    protected $nowPage = 1;

    /**
     * 每页记录数量
     */
    protected $pageNum = 20;

    /**
     * 当前页面url
     */
    protected $link = '/';

    /**
     * 列表类型
     */
    protected $type = 'new';

    /**
     * 查询结果集
     */
    protected $records;

    /**
     * 热门订阅
     */
    protected $highFollowers;

    public function __construct(WebsiteRepositry $websiteRepositry, ArticleRepositry $articleRepositry)
    {
        $this->websiteRepositry = $websiteRepositry;

        $this->articleRepositry = $articleRepositry;

        $this->records = new Collection();

        $this->highFollowers = new Collection();
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            't' => 'string|in:new,hot',
            'p' => 'int|min:1',
        ]);

        $this->type = $request->has('t') ? $request->input('t') : 'new';
        $this->nowPage = $request->has('p') ? $request->input('p') : 1;

        $offset = ($this->nowPage - 1) * $this->pageNum;

        if ($this->type == 'new') {
            $this->records = $this->articleRepositry->news($offset, $this->pageNum + 1);
        } else {
            $this->records = $this->articleRepositry->hots($offset, $this->pageNum + 1);
        }

        $this->highFollowers = $this->websiteRepositry->highFollowerWebsite();

        return view('welcome', $this->genVal());
    }

    protected function genVal()
    {
        $urlParams = [];

        if ($this->type != 'new') {
            $urlParams[] = 't=' . $this->type;
        }

        return [
            'nowPage' => $this->nowPage,
            'overagePage' => ceil($this->records->count() / $this->pageNum),
            'pageNum' => $this->pageNum,
            'type' => $this->type,
            'link' => $this->link . ($urlParams ? '?' . $urlParams[0] . '&' : '?'),
            'records' => $this->records,
            'highFollowers' => $this->highFollowers,
        ];
    }
}
