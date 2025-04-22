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
$router->get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});
$router->post('/api/auth/register', 'UserController@register');
$router->post('/api/auth/login', 'UserController@login');

$router->post('/sent-verify-link', 'UserController@SendVerificationLink');
$router->get('/verifyemail', 'UserController@verifyEmail');

// $router->get('/verification-error', 'UserController@verificationError');
// $router->get('/already-verified', 'UserController@alreadyVerified');
// $router->get('/verification-success', 'UserController@verificationSuccess');
// $router->post('/filluserinfo', 'UserController@fillUserinfo');

$router->get('{any: .*}', function () {
    return response()->json([
        'success' => false,
        'error' => 'Not Found',
        'status' => 404
    ], 404);
});
