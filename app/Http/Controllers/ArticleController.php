<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Article;
use App\Unread;

class ArticleController extends Controller
{
    public function index(Request $request, $id)
    {
        $article = Article::where([
            ['id', $id], ['status', 0]
        ])->first();

        if (!$article) {
            abort(404);
        }

        if (Auth::check()) {
            if ($request->has('t') and $request->input('t') == 'unread') {
                Unread::where([
                    ['user_id', Auth::user()->id],
                    ['website_id', $article->website_id],
                    ['article_id', $article->id]
                ])->delete();
            }
        }

        return redirect($article->link . '?utm_source=' . env('APP_HOST') . '&utm_medium=' . env('APP_HOST'));
    }

    public function show(Request $request, $id)
    {
        $article = Article::where([
            ['id', $id], ['status', 0]
        ])->first();

        if (!$article) {
            abort(404);
        }

        if (Auth::check()) {
            if ($request->has('t') and $request->input('t') == 'unread') {
                Unread::where([
                    ['user_id', Auth::user()->id],
                    ['website_id', $article->website_id],
                    ['article_id', $article->id]
                ])->delete();
            }
        }

        return view('article/detail', ['article' => $article]);
    }
}
