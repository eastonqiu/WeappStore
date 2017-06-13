<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api.throttle', 'limit' => 100, 'expires' => 5], function ($api) {
    // protected
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('/profile', 'App\Http\Controllers\API\UserController@profile');
        $api->get('/logout', 'App\Http\Controllers\API\UserController@logout');
    });

    // public
    $api->post('/login', 'App\Http\Controllers\API\AuthController@login');
    $api->get('/refresh_token', 'App\Http\Controllers\API\AuthController@refreshToken');

    // for device
    $api->group(['middleware' => 'device.auth', 'prefix' => 'device', 'namespace' => 'App\Http\Controllers\API\Device'], function ($api) {
        // sync
        $api->get('/sync_setting', 'DeviceController@syncSetting');
        $api->post('/sync_battery', 'DeviceController@syncBattery');
        $api->post('/remove_battery', 'DeviceController@removeBattery');
        $api->get('/get_logs_token', 'DeviceController@getLogsToken');

        // business
        $api->post('/borrow_confirm', 'DeviceController@borrowConfirm');
        $api->post('/return_back', 'DeviceController@returnBack');

    });

});
