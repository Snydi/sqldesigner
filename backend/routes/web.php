<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/blog', fn() => view('blog.index'));
Route::get('/blog/mysql-workbench-alternative', fn() => view('blog.mysql-workbench-alternative'));
Route::get('/blog/how-to-design-mysql-database-schema', fn() => view('blog.how-to-design-mysql-database-schema'));
Route::get('/blog/er-diagram-tool-online', fn() => view('blog.er-diagram-tool-online'));
Route::get('/blog/mysql-foreign-key', fn() => view('blog.mysql-foreign-key'));
Route::get('/blog/mysql-data-types', fn() => view('blog.mysql-data-types'));
Route::get('/blog/database-normalization', fn() => view('blog.database-normalization'));
Route::get('/blog/how-to-draw-er-diagram', fn() => view('blog.how-to-draw-er-diagram'));
Route::get('/blog/mysql-vs-postgresql', fn() => view('blog.mysql-vs-postgresql'));
Route::get('/sitemap', fn() => view('sitemap'));

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

Route::middleware('admin')->group(function () {
    Route::get('/admin', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
    Route::post('/admin/impersonate/{user}', [AdminController::class, 'impersonate'])->name('admin.impersonate');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');







