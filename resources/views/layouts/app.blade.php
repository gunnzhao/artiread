<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="一个用来订阅站点文章的网站，将想要关注的博客、资讯、文集等网站聚合在一起，便于阅读。">
        <meta name="keywords" content="RSS,聚合阅读,文章订阅,文章阅读">
        <meta name="author" content="artiread.com">
        <link rel="icon" href="{{ config('app.url') }}/favicon.ico">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Artiread') }} - @yield('title')</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
        <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
        @yield('page_css')
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom box-shadow">
            <div class="container">
                <a class="navbar-brand mb-0 h1" href="{{ Auth::check() ? '/follow' : url('/') }}">
                    <i class="fa fa-rss-square" aria-hidden="true"></i> {{ config('app.name', 'Artiread') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="/follow">订阅</a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">首页</a>
                        </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="/find">发现</a>
                        </li>
                    </ul>

                    @guest
                        <a class="btn btn-outline-secondary btn-sm my-0 mr-2" href="{{ route('login') }}">登录</a>
                        @if (Route::has('register'))
                            <a class="btn btn-outline-secondary btn-sm my-0" href="{{ route('register') }}">注册</a>
                        @endif
                    @else
                        <div class="dropdown setting-menu">
                            <a class="dropdown-toggle p-0 text-dark" href="#" id="dropdown01" data-toggle="dropdown" data-hover="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="/{{ Auth::user()->avatar ? 'storage/avatars/' . Auth::user()->avatar : 'avatar/' . Auth::user()->id }}" style="width:30px;" class="rounded-circle"> {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item" href="/bookmark">我的收藏</a>
                                <a class="dropdown-item" href="/setting/info">编辑资料</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">退出</a>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        @yield('content')

        <footer class="w-100 pt-4 bg-dark text-white">
            <div class="container">
                <div class="row py-2">
                    <div class="col-md-3">
                        <h6 class="py-1 text-center">关于 {{ config('app.name', 'Artiread') }}</h6>
                        <p>
                            <a href="/about" style="line-height:1.7em;font-size:14px;" class="text-white">Artiread 是一个用来订阅站点文章的网站，将想要关注的博客、资讯、文集等网站聚合在一起，便于阅读。</a>
                        </p>
                    </div>

                    <div class="col-md-4 offset-md-1 text-center">
                        <h6 class="py-1">站点信息</h6>
                        <ul class="list-unstyled text-small">
                            <li class="py-1"><a class="text-muted" href="/about">关于本站</a></li>
                            <li class="py-1 text-muted">QQ群 32542534</li>
                        </ul>
                    </div>

                    <div class="col-md-4 text-center">
                        <h6 class="py-1">其他信息</h6>
                        <ul class="list-unstyled text-small">
                            <li class="py-1"><a class="text-muted" href="/contact">联系站长</a></li>
                            <li class="py-1"><a class="text-muted" href="/contact">商务合作</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/popper.js/1.15.0/umd/popper.js"></script>
        <script src="https://cdn.bootcss.com/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        @yield('page_js')
    </body>
</html>