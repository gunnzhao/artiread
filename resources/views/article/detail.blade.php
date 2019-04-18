@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-12">
            <div class="bg-white py-3 px-5 mb-3">
                <h3>{{ $article->title }}</h3>

                <p class="text-secondary">
                    <a href="/website/{{ $article->website_id }}" class="text-secondary">{{ $article->website->name }}</a>
                    {{ $article->publish_time->format('Y年m月d日') }}
                    <a href="/article/{{ $article->id }}">原文链接</a>
                </p>

                <div class="artiread-content">
                    {!! $article->content !!}
                </div>

                <p>
                    <a href="/article/{{ $article->id }}">原文链接</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
