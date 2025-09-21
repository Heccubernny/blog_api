<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', fn () => view('posts.index'));
Route::get('/posts/{id}', fn () => view('posts.show'));
;

// Auth
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'));
Route::get('/author/dashboard', fn () => view('authors.dashboard'));
Route::get('/author/posts', fn () => view('authors.posts'));
