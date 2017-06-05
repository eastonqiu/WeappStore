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

Route::group(['prefix' => 'v1','middleware' => 'auth:api'], function () {
    //    Route::resource('task', 'TasksController');

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_api_routes
});

Route::group(['prefix' => 'api', 'middleware'=>'api', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1'], function () {
        // require config('infyom.laravel_generator.path.api_routes');

        Route::get('/user', function (Request $request) {
		    return "{'a':2}";
		});
    });
});

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
});
