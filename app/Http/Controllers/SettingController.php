<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Repositries\LocationRepositry;
use App\Rules\ValidatePassword;


class SettingController extends Controller
{
    protected $locationRepositry;

    public function __construct(LocationRepositry $locationRepositry)
    {
        $this->middleware('auth');

        $this->locationRepositry = $locationRepositry;
    }

    public function info()
    {
        list($provinces, $cities) = $this->locationRepositry->getProvincesAndCities();
        return view('setting/info', ['provinces' => $provinces, 'cities' => $cities]);
    }

    public function editInfo(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:4|max:255|unique:users,name,' . Auth::user()->id,
            'gender' => 'required|integer|in:1,2',
            'province' => 'required|integer',
            'city' => 'required|integer',
        ];
        $this->validate($request, $rules);
        
        Auth::user()->name = $request->input('name');
        Auth::user()->gender = $request->input('gender');
        Auth::user()->province = $request->input('province');
        Auth::user()->city = $request->input('city');
        $res = Auth::user()->save();

        return back()->with('status', '更新成功！');
    }

    public function avatar()
    {
        return view('setting/avatar');
    }

    public function editAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|max:5120'
        ]);

        if (!$request->hasFile('avatar')) {
            return back()->with('status', '上传文件有误！');
        }

        if (!$request->file('avatar')->isValid()) {
            return back()->with('status', '上传文件有误！');
        }

        $fileName = Auth::user()->id . '.' . $request->file('avatar')->extension();
        $path = $request->file('avatar')->storeAs('avatars', $fileName);
        if (!$path) {
            return back()->with('status', '上传文件失败，请稍后重试！');
        }
        
        Auth::user()->avatar = $path;
        Auth::user()->save();

        return back();
    }

    public function email()
    {
        return view('setting/email');
    }

    public function editEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::user()->id,
        ]);

        Auth::user()->email = $request->input('email');
        Auth::user()->save();
        return back()->with('status', '更新成功！');
    }

    public function password()
    {
        return view('setting/password');
    }

    public function editPassword(Request $request)
    {
        $request->validate([
            'origin_password' => ['required', 'string', 'min:8', new ValidatePassword(Auth::user())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->password = Hash::make($request->input('password'));
        Auth::user()->save();
        return back()->with('status', '更新成功！');
    }
}
