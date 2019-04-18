<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('/about', function() {
    return view('about');
});

Auth::routes();

Route::get('/follow', 'FollowController@index');
Route::get('/follow-manage', 'FollowManageController@index');

Route::post('/unread', 'UnreadController@index');
Route::post('/unread/clean', 'UnreadController@clean');

Route::get('/find', 'FindController@index');

Route::get('/avatar/{id}', 'AvatarController@show');

Route::get('/article/detail/{id}', 'ArticleController@show');
Route::get('/article/{id}', 'ArticleController@index');

Route::get('/bookmark', 'BookmarkController@index');
Route::post('/bookmark/add', 'BookmarkController@store');
Route::post('/bookmark/delete', 'BookmarkController@destory');

Route::post('/website/follow', 'WebsiteFollowController@index');
Route::get('/website/{id}', 'WebsiteController@show');


Route::get('/setting/info', 'SettingController@info');
Route::put('/setting/info', 'SettingController@editInfo');

Route::get('/setting/avatar', 'SettingController@avatar');
Route::post('/setting/avatar', 'SettingController@editAvatar');

Route::get('/setting/email', 'SettingController@email');
Route::put('/setting/email', 'SettingController@editEmail');

Route::get('/setting/password', 'SettingController@password');
Route::put('/setting/password', 'SettingController@editPassword');
