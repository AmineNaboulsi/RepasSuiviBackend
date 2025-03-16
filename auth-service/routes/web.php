<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use App\Http\Controllers\UserController;
use App\Models\User;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json(['message' => 'Auth Service']);
});

$router->post('register', 'UserController@register');
$router->post('login', 'UserController@login');

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('me', 'App\Http\Controllers\UserController@me');
    $router->post('logout', 'App\Http\Controllers\UserController@logout');

    });
});
$router->get('verify-email/{token}', 'UserController@verifyEmail');

$router->get('{any: .*}', function () {
    return response()->json([
        'success' => false,
        'error' => 'Not Found',
        'status' => 404
    ], 404);
});
