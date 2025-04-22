<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Services\RabbitMQService;

Route::get('/', function () {
    return 'welcome meal service';
});
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], Response::HTTP_OK);
});

Route::get('/send-message', function () {
    $mq = new RabbitMQService();
    $mq->publish('lol', ['message' => 'Hello from Laravel']);
    return 'Message sent!';
});
