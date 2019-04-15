<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Subscribe;
use App\Website;
use App\UserWebsite;
use App\Unread;

class WebsiteFollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'website_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => -1, 'msg' => '请求参数有误']);
        }

        $website = Website::where([
            ['id', $request->input('website_id')],
            ['status', 0]
        ])->first();

        if (!$website) {
            return response()->json(['status' => -1, 'msg' => '站点不存在']);
        }

        $res = Auth::user()->websites()->toggle($website);
        if (!$res) {
            return response()->json(['status' => -1, 'msg' => '订阅失败，请稍后重试！']);
        }

        if ($res['attached']) {
            $website->increment('followers');

            UserWebsite::where([
                ['user_id', Auth::user()->id], ['website_id', $website->id]
            ])->update([
                'last_unread_check_time' => date('Y-m-d H:i:s'),
                'last_update_time' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['status' => 0, 'data' => ['type' => 'follow']]);
        } else {
            $website->decrement('followers');

            // 清除未读记录
            Unread::where([
                ['user_id', Auth::user()->id], ['website_id', $website->id]
            ])->delete();

            return response()->json(['status' => 0, 'data' => ['type' => 'unfollow']]);
        }
    }
}
