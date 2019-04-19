@extends('layouts.app')

@section('title', '重置密码')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-sm-8 offset-sm-2">
            <div class="card">
                <div class="card-header">重置密码</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="inputEmail" class="col-sm-3 col-form-label text-right">Email</label>
                            <div class="col-sm-7">
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="inputEmail" value="{{ $email ?? old('email') }}" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-3 col-form-label text-right">密码</label>
                            <div class="col-sm-7">
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="inputPassword" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="passwordConfirm" class="col-sm-3 col-form-label text-right">确认密码</label>
                            <div class="col-sm-7">
                                <input id="passwordConfirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-7 offset-sm-3">
                                <button type="submit" class="btn btn-primary">重置密码</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
