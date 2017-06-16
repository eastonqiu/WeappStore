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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'Admin\HomeController@index');
});

Auth::routes();

Route::any('wechat', 'WechatController@serve');
Route::any('pay_notify', 'WechatController@payNotify');

// Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
// 	Route::get('wechat_auth', 'WechatController@auth');
// });

Route::group(['prefix' => 'borrow', 'middleware' => ['web.user']], function () {
    Route::get('/{deviceId}', 'BorrowController@index')->where('deviceId', '[0-9]+');;
    Route::get('/order', 'BorrowController@order');
});

Route::group(['prefix' => 'admin', 'middleware'=>'auth', 'namespace' => 'Admin'], function () {
	Route::get('/', 'HomeController@index');
	Route::get('/home', 'HomeController@index');
	Route::resource('permissions', 'PermissionController');
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');
});
