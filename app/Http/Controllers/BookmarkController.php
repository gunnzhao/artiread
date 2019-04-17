<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;
use Auth;
use App\Article;
use App\Bookmark;

class BookmarkController extends Controller
{
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
    protected $link = '/bookmark';

    /**
     * 搜索关键词
     */
    protected $keywords = '';

    /**
     * 订阅列表
     */
    protected $articles;

    /**
     * 收藏时间列表
     */
    protected $bookmarkTimes = [];

    public function __construct()
    {
        $this->middleware('auth');

        $this->articles = new Collection();
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
            $this->articles = Bookmark::where([
                ['user_id', Auth::user()->id]
            ])->latest('created_at')->offset($offset)->limit($this->pageNum + 1)->get();
        } else {
            $bookmarks = Bookmark::where('user_id', Auth::user()->id)->get();

            if ($bookmarks->count()  > 0) {
                $articleIds = [];
                foreach ($bookmarks as $bookmark) {
                    $articleIds[] = $bookmark->article_id;
                    $this->bookmarkTimes[$bookmark->article_id] = $bookmark->created_at->format('Y年m月d日');
                }

                $this->articles = Article::whereIn('id', $articleIds)->where([
                    ['status', 0],
                    ['title', 'like', '%' . $this->keywords . '%']
                ])->latest('created_at')->offset($offset)->limit($this->pageNum + 1)->get();
            }
        }

        return view('bookmark/index', $this->genVal());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        $article = Article::where([
            ['id', $request->input('article_id')],
            ['status', 0]
        ])->first();
        if (!$article) {
            return response()->json(['status' => -1, 'msg' => '文章不存在']);
        }

        $bookmark = Bookmark::where([
            ['user_id', Auth::user()->id],
            ['article_id', $article->id]
        ])->first();
        if ($bookmark) {
            return response()->json(['status' => 0]);
        }

        $bookmark = Bookmark::create([
            'user_id' => Auth::user()->id,
            'website_id' => $article->website_id,
            'article_id' => $article->id
        ]);

        if ($bookmark) {
            $article->increment('mark');
            $article->save();

            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => -1, 'msg' => '收藏失败，请稍后重试。']);
    }

    public function destory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        Bookmark::where([
            ['user_id', Auth::user()->id], ['article_id', $request->input('article_id')]
        ])->delete();

        Article::where('id', $request->input('article_id'))->decrement('mark');

        return response()->json(['status' => 0]);
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
            'overagePage' => ceil($this->articles->count() / $this->pageNum),
            'keywords' => $this->keywords,
            'articles' => $this->articles,
            'bookmarkTimes' => $this->bookmarkTimes,
        ];
    }
}
