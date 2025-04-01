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

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('me', 'UserController@me');
    $router->post('logout', 'UserController@logout');
});


$router->post('/sent-verify-link', 'UserController@SendVerificationLink');
$router->get('/verify-email', 'UserController@verifyEmail');

$router->get('/verification-error', 'UserController@verificationError');
$router->get('/already-verified', 'UserController@alreadyVerified');
$router->get('/verification-success', 'UserController@verificationSuccess');

$router->get('/users', function () use ($router) {
    return User::all();
});

$router->get('{any: .*}', function () {
    return response()->json([
        'success' => false,
        'error' => 'Not Found',
        'status' => 404
    ], 404);
});
