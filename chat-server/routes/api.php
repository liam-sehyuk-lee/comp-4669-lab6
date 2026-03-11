<?php

use App\Http\Controllers\Centrifuge\CentrifugeController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Room\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('rooms', [RoomController::class, 'store']);
Route::get('rooms', [RoomController::class, 'index']);

Route::post('messages/{room}', [MessageController::class, 'send']);
Route::get('messages/{room}', [MessageController::class, 'index']);
Route::get('ws_token', [CentrifugeController::class, 'getToken']);

Route::get('posts', [PostController::class, 'index']);
Route::post('posts', [PostController::class, 'store']);
Route::delete('posts/{post}', [PostController::class, 'destroy']);
Route::put('posts/{post}', [PostController::class, 'update']);
