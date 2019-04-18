@extends('layouts.app')

@section('title', $website->name)

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-12">
            <div class="bg-white py-3 px-5">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <p class="h5 m-0 text-center">{{ $website->name }}</p>
                        <p class="text-center">
                            <a href="{{ $website->scheme }}://{{ $website->host }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" target="_blank" class="text-secondary">{{ $website->host }}</a>
                        </p>
                        <p class="text-secondary text-center" style="font-size:16px;">{{ $website->description }}</p>
                        <p class="text-secondary text-center">
                            @auth
                                @csrf
                                
                                @if (Auth::user()->isFollowed($website->id))
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="follow">已订阅</button>
                                @else
                                    <button type="button" class="btn btn-outline-success btn-sm" id="follow">订阅</button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm">订阅</a>
                            @endauth
                            <span id="followers">{{ $website->followers }}</span>订阅
                        </p>
                    </li>

                    @if ($articles->count() > 0)
                        @foreach ($articles as $article)
                            <li class="list-group-item px-0 py-5">
                                <h5 class="mb-3">
                                    <a href="{{ $article->link }}?utm_source={{ env('APP_HOST') }}&utm_medium={{ env('APP_HOST') }}" target="_blank" class="text-dark">{{ $article->title }}</a>
                                </h5>
                                <p class="text-secondary">{{ $article->publish_time->format('Y年m月d日') }}</p>
                                <p class="text-secondary">{{ $article->description }}</p>
                            </li>
                        @endforeach

                        <li class="list-group-item px-0">
                            <p class="text-secondary text-center">仅显示最近{{ $articles->count() }}篇文章</p>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
var website_id = {{ $website->id }};
var followers = {{ $website->followers }};

$(function(){
    $('#follow').click(function() {
        $('#follow').prop('disabled', true);

        $.post('/website/follow', {'_token': $('input[name="_token"]').val(), 'website_id': website_id}, function(res) {
            if (res.status == 0) {
                if (res.data.type == 'follow') {
                    $('#follow').removeClass('btn-outline-success');
                    $('#follow').addClass('btn-outline-secondary');
                    $('#follow').html('已订阅');

                    followers++;
                } else {
                    $('#follow').removeClass('btn-outline-secondary');
                    $('#follow').addClass('btn-outline-success');
                    $('#follow').html('订阅');

                    followers--;
                }
                
                $('#followers').html(followers);
            }
            $('#follow').prop('disabled', false);
        });
    });
});
</script>
@endsection
