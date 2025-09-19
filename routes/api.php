<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Posts\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());


Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('auth.register');
    Route::post('login', 'login')->name('auth.login');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', 'logout')->name('auth.logout');
        Route::get('user', 'user')->name('auth.user');
    });
});


Route::controller(PostController::class)->prefix('posts')->group(function () {
    Route::get('/', 'index')->name('posts.index');
    Route::get('/{post}', 'show')->name('posts.show');
});


Route::middleware('auth:api')->prefix('posts')->controller(PostController::class)->group(function () {
    Route::get('/trashed', 'trashed')->name('posts.trashed');
    Route::patch('/{id}/restore', 'restore')->name('posts.restore');
    Route::delete('/{id}/force-delete', 'forceDelete')->name('posts.forceDelete');
    Route::patch('/{post}/publish', 'publish')->name('posts.publish');
    Route::patch('/{post}/unpublish', 'unpublish')->name('posts.unpublish');

    Route::post('/', 'store')->name('posts.store');
    Route::put('/{post}', 'update')->name('posts.update');
    Route::patch('/{post}', 'update');
    Route::delete('/{post}', 'destroy')->name('posts.destroy');
});
