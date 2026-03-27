<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::prefix('/blog')->group(function () {
    Route::get('/', fn() => view('blog.index'));
    Route::get('/mysql-workbench-alternative', fn() => view('blog.mysql-workbench-alternative'));
    Route::get('/how-to-design-mysql-database-schema', fn() => view('blog.how-to-design-mysql-database-schema'));
    Route::get('/er-diagram-tool-online', fn() => view('blog.er-diagram-tool-online'));
    Route::get('/mysql-foreign-key', fn() => view('blog.mysql-foreign-key'));
    Route::get('/mysql-data-types', fn() => view('blog.mysql-data-types'));
    Route::get('/database-normalization', fn() => view('blog.database-normalization'));
    Route::get('/how-to-draw-er-diagram', fn() => view('blog.how-to-draw-er-diagram'));
    Route::get('/mysql-vs-postgresql', fn() => view('blog.mysql-vs-postgresql'));
});
Route::get('/sitemap', fn() => view('sitemap'));

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::prefix('/admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
        Route::post('/impersonate/{user}', [AdminController::class, 'impersonate'])->name('admin.impersonate');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

Route::prefix('/auth')->where(['driver' => 'google|github|gitlab'])->group(function () {
    Route::get('/{driver}', [AuthController::class, 'oauthRedirect']);
    Route::get('/{driver}/callback', [AuthController::class, 'oauthCallback']);
});

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');







