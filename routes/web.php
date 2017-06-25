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

Route::group(['middleware' => ['web.user']], function () {
    // 租借模块
    Route::get('/borrow/{deviceId}', 'BorrowController@index')->where('deviceId', '[0-9]+');
    Route::get('/borrow/order', 'BorrowController@order');

    // 用户模块
    Route::get('/user/profile', 'UserController@profile');
    Route::get('/user/withdraw', 'UserController@withdraw');
    Route::get('/user/withdraw_apply', 'UserController@withdrawApply');
    Route::get('/user/orders', 'UserController@orders');
    Route::get('/user/withdraws', 'UserController@withdraws');
});

// 维护模块
Route::group(['middleware' => ['web.maintain']], function () {
    Route::get('/maintain/install/{deviceId}', 'MaintainController@install')->where('deviceId', '[0-9]+');
});

Route::group(['prefix' => 'admin', 'middleware'=>'auth', 'namespace' => 'Admin'], function () {
	Route::get('/', 'HomeController@index');
	Route::get('/home', 'HomeController@index');
	Route::resource('permissions', 'PermissionController');
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');
});
