@extends('layouts.app')

@section('title', '我的订阅')

@section('content')
<div class="container">
    <div class="row my-4">
		<div class="col-md-3">
            <div class="bg-white">
                <div class="py-3 px-4 border-bottom d-flex">
					<h6 class="m-0 mr-auto" style="padding-top: 3px;">我的订阅</h6>
					<a href="/follow-manage" data-toggle="tooltip" data-placement="top" title="管理订阅"><i class="fa fa-cog text-secondary" aria-hidden="true"></i></a>
				</div>
                
                <div class="list-group list-group-flush subescribe-list">
                    @if ($feeds->count() == 0)
                        <a href="/find" class="list-group-item list-group-item-action border-0">
                            <i class="fa fa-plus-square-o" aria-hidden="true"></i> 添加订阅
                        </a>
                    @endif

                    @foreach ($feeds as $feed)
                        @if ($feed->website_id == $websiteId)
                            <a href="/follow?w={{ $feed->website_id }}{{ isset($unread[$feed->website_id]) ? '&t=unread' : '' }}" class="list-group-item list-group-item-action border-0 text-truncate active">
                                @if (isset($unread[$feed->website_id]))
                                    <span class="badge badge-pill badge-secondary" id="unread-{{ $feed->website_id }}">{{ $unread[$feed->website_id] < 1000 ? $unread[$feed->website_id] : '...'}}</span>
                                @else
                                    <span class="badge badge-pill badge-secondary d-none" id="unread-{{ $feed->website_id }}">0</span>
                                @endif
                                {{ $feed->websites->name }}
                            </a>
                        @else
                            <a href="/follow?w={{ $feed->website_id }}{{ isset($unread[$feed->website_id]) ? '&t=unread' : '' }}" class="list-group-item list-group-item-action border-0 text-truncate text-secondary">
                                @if (isset($unread[$feed->website_id]))
                                    <span class="badge badge-pill badge-secondary" id="unread-{{ $feed->website_id }}">{{ $unread[$feed->website_id] < 1000 ? $unread[$feed->website_id] : '...'}}</span>
                                @else
                                    <span class="badge badge-pill badge-secondary d-none" id="unread-{{ $feed->website_id }}">0</span>
                                @endif
                                {{ $feed->websites->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
		</div>
		
        <div class="col-md-9">
            <div class="bg-white">
                <div class="py-3 px-4">
					<div class="row">
						<div class="col-md-4 pt-2">
                            <a href="/follow{{ $websiteId > 0 ? '?w=' . $websiteId : '?t=all' }}" class="btn btn-outline-primary btn-sm{{ $type == 'all' ? ' active' : '' }}" role="button" aria-pressed="true">全部</a>
                            <a href="/follow{{ $websiteId > 0 ? '?w=' . $websiteId . '&' : '?' }}t=unread" class="btn btn-outline-primary btn-sm{{ $type == 'unread' ? ' active' : '' }}" role="button" aria-pressed="true">
                                未读
                                @if ($websiteId > 0)
                                    @if (isset($unread[$websiteId]))
                                        <span id="total-unread">{{ $unread[$websiteId] < 1000 ? $unread[$websiteId] : '...' }}</span>
                                    @else
                                        <span id="total-unread" class="d-none">0</span>
                                    @endif
                                @else
                                    @if (!empty($unread))
                                        <span id="total-unread">{{ array_sum($unread) < 1000 ? array_sum($unread) : '...' }}</span>
                                    @else
                                        <span id="total-unread" class="d-none">0</span>
                                    @endif
                                @endif
                            </a>

                            @if ($type == 'unread' and (isset($unread[$websiteId]) or !empty($unreadArticleIds)))
                                <button type="button" id="unread-clear" class="btn btn-outline-secondary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="全部标记为已读">全部已读</button>
                            @endif
						</div>

						<div class="col-md-5 offset-md-3">
                            @if ($websiteId > 0)
                                <form action="/follow" method="GET" name="searchArticle">
                                    <div class="input-group">
                                        <input type="hidden" name="w" value="{{ $websiteId }}">
                                        <input type="text" name="q" class="form-control" placeholder="查找文章" aria-label="查找文章" aria-describedby="basic-addon2" value="{{ $keywords ? $keywords : '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </form>
                            @endif
						</div>
					</div>
                </div>

                <ul class="list-group list-group-flush px-4">
                    @if ($articles->count() == 0)
                        <li class="list-group-item py-4 px-0 article">
                            <div class="alert alert-warning" role="alert">这里没有一篇文章......</div>
                        </li>
                    @endif
                    @foreach ($articles as $article)
                        <li class="list-group-item py-4 px-0 article">
                            <h5>
                                @if ($type == 'unread' or in_array($article->id, $unreadArticleIds))
                                    <a href="/article/{{ $article->id }}?t=unread" target="_blank" class="text-dark">{{ $article->title }}</a>
                                @else
                                    <a href="/article/{{ $article->id }}" target="_blank" class="text-dark">{{ $article->title }}</a>
                                @endif
                            </h5>

                            <p>
                                <small>
                                    <a href="/website/{{ $article->website->id }}">{{ $article->website->name }}</a>
                                    {{ $article->publish_time->diffForHumans() }}
                                </small>
                            </p>

                            @if ($article->cover_pic)
                                <span style="background-image:url({{ asset('/storage/cover_img/' . $article->cover_pic) }})" class="float-left cover-pic mr-3"></span>
                            @endif

                            <p style="line-height:25px;{{ $article->cover_pic ? 'height:125px;' : '' }}">
                                @if ($type == 'unread' or in_array($article->id, $unreadArticleIds))
                                    <a href="/article/{{ $article->id }}?t=unread" target="_blank" class="text-secondary">{{ $article->description }}</a>
                                @else
                                    <a href="/article/{{ $article->id }}" target="_blank" class="text-secondary">{{ $article->description }}</a>
                                @endif
                            </p>
                            
                            <p class="mb-0">
                                @if (!in_array($article->id, $bookmarkArticleIds))
                                    <button type="button" class="btn btn-outline-secondary btn-sm bookmark" data-index="{{ $article->id }}" data-toggle="tooltip" data-placement="top" title="收藏该文章">
                                        <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success btn-sm bookmark" data-index="{{ $article->id }}" disabled>
                                        <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                                    </button>
                                @endif

                                @if ($type == 'unread' or in_array($article->id, $unreadArticleIds))
                                    <a href="/article/detail/{{ $article->id }}?t=unread" target="_blank" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="查看文章副本">
                                        <i class="fa fa-clone" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <a href="/article/detail/{{ $article->id }}" target="_blank" class="btn btn-outline-secondary btn-sm" data-toggle="tooltip" data-placement="top" title="查看文章副本">
                                        <i class="fa fa-clone" aria-hidden="true"></i>
                                    </a>
                                @endif

                                @if ($type == 'unread' or in_array($article->id, $unreadArticleIds))
                                    <button type="button" class="btn btn-outline-secondary btn-sm unread-clear-one" data-website="{{ $article->website_id }}" data-article="{{ $article->id }}" data-toggle="tooltip" data-placement="top" title="标记为已读">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </button>
                                @endif
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
    </div>
</div>

@csrf

@component('components.backtop')
@endcomponent

@endsection

@section('page_js')
<script type="text/javascript">
var pull_list = @json($pullArr);
var website_id = {{ $websiteId }};

$(function(){
    if (pull_list.length > 0) {
        for (i = 0; i < pull_list.length; i++) {
            $.post('/unread', {'_token': $('input[name="_token"]').val(), 'w': pull_list[i]}, function(res) {
                if (res.status == 0 && res.data.length > 0) {
                    for (j = 0; j < res.data.length; j++) {
                        unread_num = $('#unread-' + res.data[j].website_id).html();
                        if (unread_num != '...') {
                            unread_num = new Number(unread_num);

                            $('#unread-' + res.data[j].website_id).html(unread_num + res.data[j].count);
                            if (unread_num == 0) {
                                $('#unread-' + res.data[j].website_id).removeClass('d-none');
                                
                                href = $('#unread-' + res.data[j].website_id).parent().attr('href');
                                $('#unread-' + res.data[j].website_id).parent().attr('href', href + '&t=unread');
                            }
                        }

                        if (res.data[j].website_id == website_id || website_id == 0) {
                            total_unread = $('#total-unread').html();
                            if (total_unread != '...') {
                                total_unread = new Number(total_unread);

                                $('#total-unread').html(total_unread + res.data[j].count);

                                if (total_unread == 0) {
                                    $('#total-unread').removeClass('d-none');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    $('#unread-clear').click(function() {
        $.post('/unread/clean', {'_token': $('input[name="_token"]').val(), 'w': website_id}, function(res) {
            if (res.status == 0) {
                if (website_id == 0) {
                    location.href = '/follow';
                } else {
                    location.href = '/follow?w=' + website_id;
                }
            }
        });
    });

    $('.bookmark').click(function() {
        var _this = $(this);
        _this.prop('disabled', true);

        $.post('/bookmark/add', {'_token': $('input[name="_token"]').val(), 'article_id': _this.data('index')}, function(res) {
            if (res.status == 0) {
                _this.removeClass('btn-outline-secondary');
                _this.addClass('btn-outline-success');
                _this.tooltip('hide');
            } else {
                _this.prop('disabled', false);
            }
        });
    });

    $(window).scroll(function() {  //只要窗口滚动,就触发下面代码
        var scrollt = document.documentElement.scrollTop + document.body.scrollTop; //获取滚动后的高度
        if ( scrollt > 200 ) {  //判断滚动后高度超过200px,就显示
            $("#back-top").fadeIn(400); //淡入
        } else {
            $("#back-top").stop().fadeOut(400); //如果返回或者没有超过,就淡出.必须加上stop()停止之前动画,否则会出现闪动
        }
    });

    $("#back-top").click(function() { //当点击标签的时候,使用animate在200毫秒的时间内,滚到顶部
        $("html, body").animate({scrollTop: "0px"}, 200);
    });

    $('.unread-clear-one').click(function() {
        var _this = $(this);
        $.post('/unread/clean', {'_token': $('input[name="_token"]').val(), 'article_id': _this.data('article')}, function(res) {
            if (res.status == 0) {
                _this.fadeOut();

                unread_num = $('#unread-' + _this.data('website')).html();
                if (unread_num != '...') {
                    unread_num = new Number(unread_num);
                    unread_num--;

                    $('#unread-' + _this.data('website')).html(unread_num);
                    if (unread_num == 0) {
                        $('#unread-' + _this.data('website')).addClass('d-none');
                        $('#unread-' + _this.data('website')).parent().attr('href', '/follow?w=' + _this.data('website'));
                    }
                }

                total_unread = $('#total-unread').html();
                if (total_unread != '...') {
                    total_unread = new Number(total_unread);
                    total_unread--;

                    $('#total-unread').html(total_unread);

                    if (total_unread == 0) {
                        if (website_id == 0) {
                            location.href = '/follow';
                        } else {
                            location.href = '/follow?w=' + website_id;
                        }
                    }
                }
            }
        });
    });
});
</script>
@endsection
