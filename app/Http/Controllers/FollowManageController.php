<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Auth;
use App\UserWebsite;
use App\Website;
use App\Repositries\UserWebsiteRepositry;

class FollowManageController extends Controller
{
    protected $userWebsiteRepositry;

    /**
     * 当前页数
     */
    protected $nowPage = 1;

    /**
     * 每页记录数量
     */
    protected $pageNum = 30;

    /**
     * 当前页面url
     */
    protected $link = '/follow-manage';

    /**
     * 搜索关键词
     */
    protected $keywords = '';

    /**
     * 订阅列表
     */
    protected $feeds;

    public function __construct(UserWebsiteRepositry $userWebsiteRepositry)
    {
        $this->middleware('auth');

        $this->userWebsiteRepositry = $userWebsiteRepositry;

        $this->feeds = new Collection();
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'p' => 'int|min:1',
            'q' => 'string|max:255',
        ]);

        $this->nowPage = $request->has('p') ? $request->input('p') : 1;
        $this->keywords = $request->has('q') ? $request->input('q') : '';

        $offset = ($this->nowPage - 1) * $this->pageNum;
        
        if (!$this->keywords) {
            $this->feeds = UserWebsite::where([
                ['user_id', Auth::user()->id]
            ])->latest('id')->offset($offset)->limit($this->pageNum + 1)->get();
        } else {
            $websiteIds = $this->userWebsiteRepositry->followWebsiteIds(Auth::user()->id, 0, 3000);

            $this->feeds = Website::whereIn('id', $websiteIds)->where([
                ['status', 0],
                ['name', 'like', '%' . $this->keywords . '%']
            ])->latest('created_at')->offset($offset)->limit($this->pageNum + 1)->get();
        }

        return view('follow/manage', $this->genVal());
    }

    private function genVal()
    {
        $urlParams = [];

        if ($this->keywords != '') {
            $urlParams[] = 'q=' . $this->keywords;
        }

        return [
            'link' => $this->link . ($urlParams ? '?' . $urlParams[0] . '&' : '?'),
            'nowPage' => $this->nowPage,
            'pageNum' => $this->pageNum,
            'overagePage' => ceil($this->feeds->count() / $this->pageNum),
            'keywords' => $this->keywords,
            'feeds' => $this->feeds,
        ];
    }
}
