@extends('layouts.app')

@section('title', '修改邮箱')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-3">
            @component('components.settingsidebar', ['item' => 'email'])
            @endcomponent
        </div>
        <div class="col-9">
            <div class="card">
                <h6 class="card-header"><i class="fa fa-envelope-o" aria-hidden="true"></i> 修改邮箱</h6>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @else
                        <div class="alert alert-warning" role="alert">
                        邮箱修改后，您将无法使用当前的邮箱进行登录，请谨慎操作。
                        </div>
                    @endif
                    <form action="/setting/email" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="inputEmail">Email</label>
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') ? old('email') : Auth::user()->email }}" require>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">更新邮箱</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
