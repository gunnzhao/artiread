@extends('layouts.app')

@section('title', '密码找回')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">密码找回</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="inputEmail" class="col-md-3 col-form-label text-md-right">Email</label>
                            <div class="col-md-7">
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="inputEmail" value="{{ old('email') }}" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputCaptcha" class="col-md-3 col-form-label text-md-right">验证码</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control{{ $errors->has('captcha') ? ' is-invalid' : '' }}" name="captcha" id="inputCaptcha" value="{{ old('captcha') }}">
                                @if ($errors->has('captcha'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('captcha') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-5">
                                <img src="{{ captcha_src() }}" style="cursor:pointer" onclick="this.src='{{ captcha_src() }}' + Math.random()">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-7 offset-md-3">
                                <button type="submit" class="btn btn-primary">发送重置密码链接</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
