<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\UserWebsite;
use App\Article;
use App\Unread;

class UnreadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'w' => 'required|array|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        $websiteIds = [];
        foreach ($request->input('w') as $websiteId) {
            if (is_numeric($websiteId)) {
                $websiteIds[] = $websiteId;
            }
        }

        if (count($websiteIds) > 1) {
            $feeds = UserWebsite::where('user_id', Auth::user()->id)
                ->whereIn('website_id', $websiteIds)->get();
        } elseif (count($websiteIds) == 1) {
            $feeds = UserWebsite::where([
                ['user_id', Auth::user()->id], ['website_id', $websiteIds[0]]
            ])->get();
        } else {
            return response()->json(['status' => -1, 'msg' => '无订阅站点']);
        }
        

        if ($feeds->count() == 0) {
            return response()->json(['status' => -1, 'msg' => '无订阅站点']);
        }

        $updateList = [];

        foreach ($feeds as $feed) {
            if ($feed->last_unread_check_time < $feed->last_update_time) {
                $updateList[] = $feed;
            }
        }

        if (empty($updateList)) {
            return response()->json(['status' => -1, 'msg' => '无最新文章']);
        }

        $result = [];
        foreach ($updateList as $feed) {
            if ($res = $this->checkUnread($feed)) {
                $result[] = $res;
            }
        }

        return response()->json(['status' => 0, 'data' => $result]);
    }

    public function clean(Request $request)
    {
        if (!$request->has('w') and !$request->has('article_id')) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        $validator = Validator::make($request->all(), [
            'w' => 'integer|min:0',
            'article_id' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        if ($request->has('article_id')) {
            $count = Unread::where([
                ['user_id', Auth::user()->id], ['article_id', $request->input('article_id')]
            ])->delete();
        } else {
            if ($request->input('w') == 0) {
                // 清除所有未读
                $count = Unread::where('user_id', Auth::user()->id)->delete();
            } else {
                $count = Unread::where([
                    ['user_id', Auth::user()->id], ['website_id', $request->input('w')]
                ])->delete();
            }
        }

        return response()->json(['status' => 0, 'data' => ['count' => $count]]);
    }

    private function checkUnread(UserWebsite $feed)
    {
        $articles = Article::where([
            ['website_id', $feed->website_id],
            ['publish_time', '>', $feed->last_unread_check_time],
            ['status', 0]
        ])->get();

        if ($articles->count() == 0) {
            // 更新未读检查时间
            $feed->last_unread_check_time = date('Y-m-d H:i:s');
            $feed->save();

            return [];
        }

        $count = 0;
        foreach ($articles as $article) {
            $record = Unread::where([
                'user_id' => Auth::user()->id,
                'website_id' => $article->website_id,
                'article_id' => $article->id
            ])->first();

            if (!$record) {
                Unread::create([
                    'user_id' => Auth::user()->id,
                    'website_id' => $article->website_id,
                    'article_id' => $article->id
                ]);
                $count++;
            }
        }

        // 更新未读检查时间
        $feed->last_unread_check_time = date('Y-m-d H:i:s');
        $feed->save();

        return ['website_id' => $feed->website_id, 'count' => $count];
    }
}
