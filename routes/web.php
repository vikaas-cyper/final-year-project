<?php

use App\Http\Controllers\BackUpDownload;
use App\Http\Controllers\UserManualController;

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

Route::get('/', function () {
    return redirect('/admin', 302);
});

// Google login
Route::get('auth/google', 'App\Http\Controllers\Auth\GoogleController@redirectToGoogle');
Route::get('auth/google/callback', 'App\Http\Controllers\Auth\GoogleController@handleGoogleCallback');

if ($loginPage = config('filament.auth.pages.login')) {
    Route::get('login', $loginPage)->name('login');
}

Route::get('backup', [BackUpDownload::class, 'download'])->name('backup');

Route::get('manual', [UserManualController::class, 'download'])->name('manual');
