@extends('layouts.app')

@section('title', '修改密码')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-sm-3">
            @component('components.settingsidebar', ['item' => 'password'])
            @endcomponent
        </div>
        <div class="col-sm-9">
            <div class="card">
                <h6 class="card-header"><i class="fa fa-lock" aria-hidden="true"></i> 修改密码</h6>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif
                    <form action="/setting/password" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="inputOriginPassword">当前密码</label>
                            <input type="password" class="form-control{{ $errors->has('origin_password') ? ' is-invalid' : '' }}" name="origin_password" required>
                            @if ($errors->has('origin_password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('origin_password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="inputPassword">新密码</label>
                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="passwordConfirm">确认新密码</label>
                            <input id="passwordConfirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">更新密码</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
