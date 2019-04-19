@extends('layouts.app')

@section('title', '欢迎来到Artiread')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-sm-9">
            <div class="bg-white">
                <div class="py-3 px-4">
                    <a href="/?t=new" class="btn btn-outline-primary btn-sm{{ $type == 'new' ? ' active' : '' }}" role="button" aria-pressed="true" data-toggle="tooltip" data-placement="top" title="发布时间排序">最新</a>
                    <a href="/?t=hot" class="btn btn-outline-primary btn-sm{{ $type == 'hot' ? ' active' : '' }}" role="button" aria-pressed="true" data-toggle="tooltip" data-placement="top" title="当日点击排序">热门</a>
                </div>

                <ul class="list-group list-group-flush px-4">
                    @foreach ($records as $record)
                        <li class="list-group-item py-4 px-0 article">
                            <h5>
                                <a href="/article/{{ $record->id }}" target="_blank" class="text-dark">{{ $record->title }}</a>
                            </h5>
                            <p>
                                <small class="text-secondary">
                                    <a href="/website/{{ $record->website->id }}">{{ $record->website->name }}</a>
                                    {{ $record->publish_time->diffForHumans() }}
                                </small>
                            </p>
                            <p class="mb-0" style="line-height:25px;">
                                <a href="/article/{{ $record->id }}" target="_blank" class="text-secondary">{{ $record->description }}</a>
                            </p>
                        </li>

                        @if ($loop->iteration == $pageNum)
                            @break
                        @endif
                    @endforeach
                </ul>

                @component('components.pagination', ['link' => $link, 'nowPage' => $nowPage, 'overagePage' => $overagePage])
                @endcomponent

                
            </div>
        </div>

        <div class="col-sm-3">
            <div class="bg-white">
                <div class="py-2 px-3 border-bottom">热门订阅</div>
                <ul class="list-group list-group-flush">
                    @foreach ($highFollowers as $highFollower)
                        <li class="list-group-item border-0 d-flex pl-2">
                            <p class="pt-2 pr-2 mb-0">
                                <span class="badge badge-pill badge-light text-secondary">{{ $loop->iteration }}</span>
                            </p>
                            <p class="text-truncate mb-0">
                                <a href="/website/{{ $highFollower->id }}" class="text-dark">{{ $highFollower->name }}</a><br>
                                <span class="text-secondary" style="font-size:12px;">{{ $highFollower->followers }} 订阅</span>
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
