@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row my-4">
        <div class="col-3">
            @component('components.settingsidebar', ['item' => 'info'])
            @endcomponent
        </div>
        <div class="col-9">
            <div class="card">
                <h6 class="card-header"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> 修改资料</h6>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif
                    <form action="/setting/info" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="inputName">用户名</label>
                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ Auth::user()->name }}" require>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="inputName">性别</label>
                            <select class="form-control" name="gender">
                                <option value="1"{{ Auth::user()->gender == 1 ? 'selected' : '' }}>男</option>
                                <option value="2"{{ Auth::user()->gender == 2 ? 'selected' : '' }}>女</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputPassword1">省份</label>
                                <select class="form-control" name="province">
                                    @if (Auth::user()->province == 0)
                                        <option value="0">请选择</option>
                                    @endif
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province['id'] }}"{{ $province['id'] == Auth::user()->province ? ' selected' : '' }}>{{ $province['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputPassword1">城市</label>
                                <select class="form-control" name="city">
                                    @if (Auth::user()->city == 0)
                                        <option value="0">请选择</option>
                                    @else
                                        @foreach ($cities[Auth::user()->province] as $city)
                                            <option value="{{ $city['id'] }}"{{ $city['id'] == Auth::user()->city ? ' selected' : '' }}>{{ $city['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">更新信息</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script type="text/javascript">
var cities = @json($cities);

$(function(){
    $('select[name="province"]').change(function() {
        var province = $(this).val();
        
        $('select[name="city"]').empty();
        for (i = 0; i < cities[province].length; i++) {
            $('select[name="city"]').append('<option value="' + cities[province][i]['id'] + '">' + cities[province][i]['name'] + '</option>');
        }
    });
});
</script>
@endsection
