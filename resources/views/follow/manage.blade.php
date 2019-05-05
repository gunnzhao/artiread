@extends('layouts.app')

@section('title', '管理订阅')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-md-12">
            <div class="bg-white py-3 px-5 mb-3">
                <form action="/follow-manage" method="GET" name="searchFeed">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="输入网站名称" aria-label="输入网站名称" aria-describedby="basic-addon2" value="{{ $keywords ? $keywords : '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">查找</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white py-3 px-4" style="margin-top:1px;">
                <a href="/find" class="d-block"><i class="fa fa-plus-square-o" aria-hidden="true"></i> 添加订阅</a>
            </div>

            @if ($overagePage > 0)
                @csrf

                @if (!$keywords)
                    @foreach ($feeds as $feed)
                        <div class="bg-white py-3 px-4" style="margin-top:1px;">
                            <div class="row">
                                <div class="col-md-8">
                                    <a href="/website/{{ $feed->websites->id }}"><strong class="text-dark">{{ $feed->websites->name }}</strong></a>
                                    <small class="text-secondary">
                                        <span id="followers-{{ $feed->websites->id }}">{{ $feed->websites->followers }}</span> 人订阅 |
                                        上次更新于{{ $feed->websites->last_update_time->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a href="/follow?w={{ $feed->websites->id }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ $feed->websites->scheme }}://{{ $feed->websites->host }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm follow" data-index="{{ $feed->websites->id }}">取消订阅</button>
                                </div>
                            </div>
                        </div>
                        
                        @if ($loop->iteration == $pageNum)
                            @break
                        @endif
                    @endforeach
                @else
                    @foreach ($feeds as $feed)
                        <div class="bg-white py-3 px-4" style="margin-top:1px;">
                            <div class="row">
                                <div class="col-md-8">
                                    <a href="/website/{{ $feed->id }}"><strong class="text-dark">{{ $feed->name }}</strong></a>
                                    <small class="text-secondary">
                                        <span id="followers-{{ $feed->id }}">{{ $feed->followers }}</span> 人订阅 |
                                        上次更新于{{ $feed->last_update_time->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a href="/follow?w={{ $feed->id }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ $feed->scheme }}://{{ $feed->host }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm follow" data-index="{{ $feed->id }}">取消订阅</button>
                                </div>
                            </div>
                        </div>
                        
                        @if ($loop->iteration == $pageNum)
                            @break
                        @endif
                    @endforeach
                @endif

                @component('components.pagination', ['link' => $link, 'nowPage' => $nowPage, 'overagePage' => $overagePage])
                @endcomponent
            @else
                <div class="bg-white py-3 px-4">
                    <div class="alert alert-danger" role="alert">找不到您想要的结果。</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
$(function(){
    $('.follow').click(function() {
        var _this = $(this);
        var followers = $('#followers-' + _this.data('index')).html();

        _this.prop('disabled', true);

        $.post('/website/follow', {'_token': $('input[name="_token"]').val(), 'website_id': _this.data('index')}, function(res) {
            if (res.status == 0) {
                if (res.data.type == 'follow') {
                    _this.removeClass('btn-outline-success');
                    _this.addClass('btn-outline-secondary');
                    _this.html('取消订阅');

                    followers++;
                } else {
                    _this.removeClass('btn-outline-secondary');
                    _this.addClass('btn-outline-success');
                    _this.html('订阅');

                    followers--;
                }
                
                $('#followers-' + _this.data('index')).html(followers);
            }
            _this.prop('disabled', false);
        });
    });
});
</script>
@endsection
