<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="{{ config('app.url') }}/favicon.ico">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Artiread') }}</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.css">
        <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
        @yield('page_css')
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom box-shadow">
            <div class="container">
                <a class="navbar-brand mb-0 h1" href="{{ url('/') }}">
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">关于</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">反馈</a>
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
                                <img src="/{{ Auth::user()->avatar }}" style="width:30px;" class="rounded-circle"> {{ Auth::user()->name }}
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
                <div class="row">
                    <div class="col-4">
                        <h6 class="py-1">关于 <i class="fa fa-rss-square" aria-hidden="true"></i> {{ config('app.name', 'Artiread') }}</h6>
                        <p>Reading是一个......</p>
                    </div>
                    <div class="col-4">
                        <h6 class="py-1">感谢</h6>
                        <ul class="list-unstyled text-small">
                            <li><a class="text-muted" href="#">ApiCat</a></li>
                            <li><a class="text-muted" href="#">Laravel</a></li>
                            <li><a class="text-muted" href="#">Bootstrap</a></li>
                            <li><a class="text-muted" href="#">Font Awesome</a></li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <h6 class="py-1">其他信息</h6>
                        <ul class="list-unstyled text-small">
                            <li class="py-1"><a class="text-muted" href="#">软件外包</a></li>
                            <li class="py-1"><a class="text-muted" href="#">商务合作</a></li>
                            <li class="py-1"><a class="text-muted" href="#">联系站长</a></li>
                            <li class="py-1"><a class="text-muted" href="#">QQ群 123456789</a></li>
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