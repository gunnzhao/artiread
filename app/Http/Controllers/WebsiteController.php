<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositries\WebsiteRepositry;
use App\Repositries\ArticleRepositry;

class WebsiteController extends Controller
{
    protected $websiteRepositry;

    protected $articleRepositry;

    public function __construct(WebsiteRepositry $websiteRepositry, ArticleRepositry $articleRepositry)
    {
        $this->websiteRepositry = $websiteRepositry;

        $this->articleRepositry = $articleRepositry;
    }

    public function show($id)
    {
        $website = $this->websiteRepositry->getById($id);
        if (!$website) {
            abort(404);
        }

        $articles = $this->articleRepositry->recentArticles($website->id);
        return view('website/detail', ['website' => $website, 'articles' => $articles]);
    }
}
