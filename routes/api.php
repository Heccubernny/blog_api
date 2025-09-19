<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Posts\PostController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});


Route::middleware('auth:api')->group(function () {
    Route::apiResource('posts', PostController::class);

    Route::patch('posts/{post}/publish', [PostController::class, 'publish']);
    Route::patch('posts/{post}/unpublish', [PostController::class, 'unpublish']);

    Route::get('posts/trashed', [PostController::class, 'trashed']);
    Route::patch('posts/{id}/restore', [PostController::class, 'restore']);
    Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDelete']);
});
