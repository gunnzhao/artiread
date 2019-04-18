@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-12">
            <div class="bg-white py-3 px-5 mb-3">
                <form action="/find" method="GET" name="searchWebsite">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="输入网站名称或Rss地址" aria-label="输入网站名称或Rss地址" aria-describedby="basic-addon2" value="{{ $keywords ? $keywords : '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">查找</button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($overagePage > 0)
                @csrf

                @foreach ($results as $result)
                    <div class="bg-white py-3 px-4" style="margin-top:1px;">
                        <div class="row">
                            <div class="col-10 pl-5 border-right">
                                <a href="/website/{{ $result->id }}"><strong class="text-dark" style="font-size:18px;">{{ $result->name }}</strong></a>
                                <a href="{{ $result->scheme }}://{{ $result->host }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" target="_blank"><small class="text-secondary">{{ $result->host }}</small></a>
                                <p class="mb-0 mt-2 text-secondary">{{ $result->description }}</p>
                            </div>

                            <div class="col-2">
                                <div class="text-center">
                                    <a href="{{ $result->scheme }}://{{ $result->host }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="访问站点">
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </a>
                                    @auth
                                        @if (Auth::user()->isFollowed($result->id))
                                            <button type="button" class="btn btn-outline-secondary btn-sm follow" data-index="{{ $result->id }}" data-toggle="tooltip" data-placement="top" title="取消订阅该站点">已订阅</button>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm follow" data-index="{{ $result->id }}" data-toggle="tooltip" data-placement="top" title="订阅该站点">订阅</button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm" data-toggle="tooltip" data-placement="top" title="订阅该站点">订阅</a>
                                    @endauth
                                </div>
                                <div class="text-center">
                                    <small class="m-0"><span id="followers-{{ $result->id }}">{{ $result->followers }}</span> 订阅</small>
                                </div>
                                <div class="text-center"><small class="text-secondary">上次更新于{{ $result->last_update_time->diffForHumans() }}</small></div>
                            </div>
                        </div>
                    </div>

                    @if ($loop->iteration == $pageNum)
                        @break
                    @endif
                @endforeach

                <div class="bg-white py-3 px-4">
                    @component('components.pagination', ['link' => $link, 'nowPage' => $nowPage, 'overagePage' => $overagePage])
                    @endcomponent
                </div>
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
                    _this.html('已订阅');

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
