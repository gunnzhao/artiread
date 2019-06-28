@extends('layouts.app')

@section('title', '我的收藏')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-md-12">
            <div class="bg-white py-3 px-5 mb-3">
                <form action="/bookmark" method="GET" name="searchFeed">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="输入文章名称" aria-label="输入文章名称" aria-describedby="basic-addon2" value="{{ $keywords ? $keywords : '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">查找</button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($overagePage > 0)
                @csrf

                @if (!$keywords)
                    @foreach ($articles as $article)
                        <div class="bg-white py-3 px-4 article" style="margin-top:1px;" id="bookmark-{{ $article->article_id }}">
                            <div class="d-flex">
                                <h5 class="mr-auto">
                                    <a href="/article/{{ $article->article_id }}" target="_blank" class="text-dark">{{ $article->articles->title }}</a>
                                </h5>

                                <button type="button" class="close mb-2" aria-label="Close" data-index="{{ $article->article_id }}" data-toggle="modal" data-target="#deleteBookmark">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <p>
                                <small class="text-secondary">
                                    来自 <a href="/website/{{ $article->website_id }}">{{ $article->articles->website->name }}</a>
                                    收藏于{{ $article->created_at->format('Y年m月d日') }}
                                </small>
                            </p>

                            <p class="mb-0" style="line-height:25px;">
                                <a href="/article/{{ $article->article_id }}" target="_blank" class="text-secondary">{{ $article->articles->description }}</a>
                            </p>
                        </div>
                    @endforeach
                @else
                    @foreach ($articles as $article)
                        <div class="bg-white py-3 px-4 article" style="margin-top:1px;" id="bookmark-{{ $article->id }}">
                            <div class="d-flex">
                                <h5 class="mr-auto">
                                    <a href="/article/{{ $article->id }}" target="_blank" class="text-dark">{{ $article->title }}</a>
                                </h5>

                                <button type="button" class="close mb-2" aria-label="Close" data-index="{{ $article->article_id }}" data-toggle="modal" data-target="#deleteBookmark">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <p>
                                <small class="text-secondary">
                                    来自 <a href="/website/{{ $article->website_id }}">{{ $article->website->name }}</a>
                                    收藏于{{ $bookmarkTimes[$article->id] }}
                                </small>
                            </p>

                            <p class="mb-0" style="line-height:25px;">
                                <a href="/article/{{ $article->id }}" target="_blank" class="text-secondary">{{ $article->description }}</a>
                            </p>
                        </div>
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

    <div class="modal fade" id="deleteBookmark" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">删除收藏</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    
                <div class="modal-body">
                    <p id="delete-bookmark-title"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="delete-confim">确定</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
$(function(){
    var title;
    var article_id;

    $('.close').click(function() {
        title = $(this).parent().children('h5').children('a').html();
        article_id = $(this).data('index');
        $('#delete-bookmark-title').html('要删除文章《' + title + '》吗？');
    });

    $('#delete-confim').click(function() {
        $.post('/bookmark/delete', {'_token': $('input[name="_token"]').val(), 'article_id': article_id}, function(res) {
            if (res.status == 0) {
                $('#bookmark-' + article_id).fadeOut();
            }
        });
        $('#deleteBookmark').modal('hide');
        
    });
});
</script>
@endsection
