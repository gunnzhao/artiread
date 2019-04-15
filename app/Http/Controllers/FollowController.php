<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Auth;
use App\UserWebsite;
use App\Unread;
use App\Bookmark;
use App\Repositries\WebsiteRepositry;
use App\Repositries\ArticleRepositry;
use App\Repositries\UnreadRepositry;

class FollowController extends Controller
{
    protected $websiteRepositry;

    protected $articleRepositry;

    protected $unreadRepositry;

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
    protected $link = '/follow';

    /**
     * 站点id
     */
    protected $websiteId = 0;

    /**
     * 列表类型
     */
    protected $type = 'all';

    /**
     * 搜索关键词
     */
    protected $keywords = '';

    /**
     * 文章结果集
     */
    protected $articles;

    /**
     * 订阅列表
     */
    protected $feeds;

    /**
     * 未读量
     */
    protected $unread = [];

    /**
     * 未读文章id
     */
    protected $unreadArticleIds = [];

    /**
     * 收藏文章id
     */
    protected $bookmarkArticleIds = [];

    /**
     * 需要拉取更新阅读量的站点数组
     */
    protected $pullArr = [];

    public function __construct(WebsiteRepositry $websiteRepositry,
        ArticleRepositry $articleRepositry, UnreadRepositry $unreadRepositry)
    {
        $this->middleware('auth');

        $this->websiteRepositry = $websiteRepositry;

        $this->articleRepositry = $articleRepositry;

        $this->unreadRepositry = $unreadRepositry;

        $this->feeds = $this->articles = new Collection();
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'w' => 'int|min:1',
            't' => 'string|in:all,unread',
            'p' => 'int|min:1',
            'q' => 'string|max:255',
        ]);

        $this->websiteId = $request->has('w') ? $request->input('w') : 0;
        $this->type = $request->has('t') ? $request->input('t') : 'all';
        $this->nowPage = $request->has('p') ? $request->input('p') : 1;
        $this->keywords = $request->has('q') ? $request->input('q') : '';

        $this->feeds = UserWebsite::where('user_id', Auth::user()->id)->latest('last_update_time')->get();
        if ($this->feeds->count() == 0) {
            return view('follow/index', $this->genVal());
        }

        $unreads = Unread::where('user_id', Auth::user()->id)->get();
        if ($unreads->count() > 0) {
            foreach ($unreads as $unread) {
                if (!isset($this->unread[$unread->website_id])) {
                    $this->unread[$unread->website_id] = 1;
                } else {
                    $this->unread[$unread->website_id]++;
                }

                if ($this->websiteId == 0) {
                    $this->unreadArticleIds[] = $unread->article_id;
                } else {
                    if ($this->websiteId == $unread->website_id) {
                        $this->unreadArticleIds[] = $unread->article_id;
                    }
                }
            }

            // 如果有未读文章，且用户没有主动选择列表类型，默认显示未读。
            if (!$request->has('t') and $this->websiteId == 0) {
                $this->type = 'unread';
            }
        }

        $websiteIds = [];

        // 将需要拉取最新未读的站点按5个一组放入pullArr中
        // 例：[[1, 2, 3, 4, 5], [6, 7, 8, 9, 10], [...]]
        $group = [];
        foreach ($this->feeds as $feed) {
            // 最后检查未读的时间小于站点更新时间的，说明要去更新未读列表了。
            if ($feed->last_unread_check_time < $feed->last_update_time) {
                $group[] = $feed->website_id;

                if (count($group) == 5) {
                    $this->pullArr[] = $group;
                    $group = [];
                }
            }

            $websiteIds[] = $feed->website_id;
        }

        // 检查剩余未满5个的组
        if (!empty($group)) {
            $this->pullArr[] = $group;
        }

        if ($this->websiteId == 0) {
            return $this->allWebsite($websiteIds);
        }
        return $this->singleWebsite();
    }

    protected function allWebsite(array $websiteIds)
    {
        $offset = ($this->nowPage - 1) * $this->pageNum;

        if ($this->type == 'all') {
            $this->articles = $this->articleRepositry->followNews($websiteIds, $offset, $this->pageNum + 1);

            $articleIds = [];
            if ($this->articles->count() > 0) {
                // 找出未读文章id
                foreach ($this->articles as $article) {
                    $articleIds[] = $article->id;
                }
                $this->unreadArticleIds = array_intersect($articleIds, $this->unreadArticleIds);
            }
        } else {
            $articleIds = $this->unreadRepositry->allArticleIds(Auth::user()->id);
            if ($articleIds) {
                $this->articles = $this->articleRepositry->someArticles($articleIds, $offset, $this->pageNum + 1);
            }
        }

        if ($articleIds) {
            $this->bookmarkArticleIds = Bookmark::where('user_id', Auth::user()->id)
                ->whereIn('article_id', $articleIds)->pluck('article_id')->toArray();
        }

        return view('follow/index', $this->genVal());
    }

    protected function singleWebsite()
    {
        $offset = ($this->nowPage - 1) * $this->pageNum;
        $articleIds = [];

        if (!$this->keywords) {
            if ($this->type == 'all') {
                $this->articles = $this->articleRepositry->followNews([$this->websiteId], $offset, $this->pageNum + 1);
                if ($this->articles->count() > 0) {
                    foreach ($this->articles as $article) {
                        $articleIds[] = $article->id;
                    }
                }
            } else {
                $articleIds = $this->unreadRepositry->singleWebsiteArticleIds(Auth::user()->id, $this->websiteId);
                if ($articleIds) {
                    $this->articles = $this->articleRepositry->someArticles($articleIds, $offset, $this->pageNum + 1);
                }
            }
        } else {
            $this->articles = $this->articleRepositry->titleSearch($this->websiteId, $this->keywords, $offset, $this->pageNum + 1);
            if ($this->articles->count() > 0) {
                foreach ($this->articles as $article) {
                    $articleIds[] = $article->id;
                }
            }
        }

        if ($articleIds) {
            $this->bookmarkArticleIds = Bookmark::where('user_id', Auth::user()->id)
                ->whereIn('article_id', $articleIds)->pluck('article_id')->toArray();
        }

        return view('follow/index', $this->genVal());
    }

    protected function genVal()
    {
        $urlParams = [];

        if ($this->type != 'all') {
            $urlParams[] = 't=' . $this->type;
        }
        
        if ($this->websiteId > 0) {
            $urlParams[] = 'w=' . $this->websiteId;
        }

        if ($this->keywords != '') {
            $urlParams[] = 'q=' . $this->keywords;
        }

        $urlParams = implode('&', $urlParams);

        return [
            'nowPage' => $this->nowPage,
            'overagePage' => ceil($this->articles->count() / $this->pageNum),
            'pageNum' => $this->pageNum,
            'link' => $this->link . ($urlParams ? '?' . $urlParams . '&' : '?'),
            'websiteId' => $this->websiteId,
            'type' => $this->type,
            'keywords' => $this->keywords,
            'articles' => $this->articles,
            'unreadArticleIds' => $this->unreadArticleIds,
            'bookmarkArticleIds' => $this->bookmarkArticleIds,
            'feeds' => $this->feeds,
            'unread' => $this->unread,
            'pullArr' => $this->pullArr,
        ];
    }
}
