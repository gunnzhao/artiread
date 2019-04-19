@extends('layouts.app')

@section('title', '修改头像')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-sm-3">
            @component('components.settingsidebar', ['item' => 'avatar'])
            @endcomponent
        </div>
        <div class="col-sm-9">
            <div class="card">
                <h6 class="card-header"><i class="fa fa-picture-o" aria-hidden="true"></i> 修改头像</h6>
                <div class="card-body">
                    <p><img src="{{ Auth::user()->avatar ? '/' . Auth::user()->avatar : '/avatar/' . Auth::user()->id }}" style="width:300px;" class="img-thumbnail"></p>
                    
                    @if (session('status'))
                        <div class="alert alert-danger" role="alert">{{ session('status') }}</div>
                    @endif

                    @if ($errors->has('avatar'))
                        <div class="alert alert-danger" role="alert">{{ $errors->first('avatar') }}</div>
                    @endif

                    <form class="mt-3" action="/setting/avatar" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="inputFile">请选择图片</label>
                            <input type="file" class="form-control-file border rounded p-2" name="avatar">
                        </div>

                        <button type="submit" class="btn btn-primary">上传头像</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
