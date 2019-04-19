@extends('layouts.app')

@section('title', '关于Artiread')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-12 bg-white py-5 px-5" style="font-size:16px;">
            <h3 class="mb-4">关于Artiread</h3>

            <p style="line-height:2em;">
                Artiread是一个用来订阅站点文章的网站，基于RSS和爬虫。将自己想要关注的博客、资讯、文集等网站进行订阅，每次这些站点的内容更新后，就会自动提醒未读。Artiread将这些站点聚合在一起，这样只用通过一个地方就能知道所有站点的更新状况。
            </p>

            <p style="line-height:2em;">
                Artiread是<code>Arti</code>cle和<code>read</code>的组合，参考自Instagram和Smartisan的起名方式。
            </p>

            <p style="line-height:2em;">
                网上相关的订阅工具有很多，大多数RSS订阅工具都是将文章在自己的界面里做了渲染，字体、布局、颜色等这些和原作者在自己网站里的风格完全不一样了。还有一些站点没有严格遵循RSS的协议，内容有比较严重的问题，获取的内容会出现不完整，甚至没有内容。
            </p>

            <p style="line-height:2em;">
                Artiread提倡到原文地址去阅读，体验那种原汁原味的感觉，同时还可以提高原文站点的访问量。
            </p>

            <p style="line-height:2em;">
                如果原文的排版读者不能接受，Artiread也提供了在站内阅读的功能。
            </p>

            <p style="line-height:2em;">
                欢迎您使用Artiread。
            </p>
        </div>
    </div>
</div>
@endsection
