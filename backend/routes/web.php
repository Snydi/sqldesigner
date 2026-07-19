<?php

declare(strict_types=1);

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::prefix('/blog')->group(function () {
    Route::get('/', fn () => view('blog.index'));
    Route::get('/mysql-foreign-key', fn () => view('blog.mysql-foreign-key'));
    Route::get('/mysql-data-types', fn () => view('blog.mysql-data-types'));
    Route::get('/database-normalization', fn () => view('blog.database-normalization'));
    Route::get('/mysql-vs-postgresql', fn () => view('blog.mysql-vs-postgresql'));
    Route::get('/database-schema-examples', fn () => view('blog.database-schema-examples'));
    Route::get('/database-designer', fn () => view('blog.database-designer'));
    Route::get('/crowfoot-notation', fn () => view('blog.crowfoot-notation'));
    Route::get('/database-ddl-comparison', fn () => view('blog.database-ddl-comparison'));
    Route::get('/best-free-erd-tools', fn () => view('blog.best-free-erd-tools'));
    Route::get('/postgresql-data-types', fn () => view('blog.postgresql-data-types'));
    Route::get('/create-database-schema-online', fn () => view('blog.create-database-schema-online'));
    Route::get('/er-diagram-maker-online', fn () => view('blog.er-diagram-maker-online'));
});
Route::get('/about', fn () => view('about'));
Route::get('/features', fn () => view('features'));
Route::get('/pricing', fn () => view('pricing'));
Route::get('/library', [LibraryController::class, 'index']);
Route::get('/sitemap', fn () => view('sitemap'));
Route::get('/privacy', fn () => view('privacy'));
Route::get('/terms', fn () => view('terms'));
Route::get('/checkout/success', [SubscriptionController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/checkout/fail', [SubscriptionController::class, 'checkoutFail'])->name('checkout.fail');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::prefix('/admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post')->middleware('throttle:5,1');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'showDashboard'])->name('admin.dashboard');
        Route::get('/library', [AdminController::class, 'showLibrary'])->name('admin.library');
        Route::get('/reviews', [AdminController::class, 'showReviews'])->name('admin.reviews');
        Route::post('/impersonate/{user}', [AdminController::class, 'impersonate'])->name('admin.impersonate');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/diagrams/{diagram}/feature', [AdminController::class, 'featureDiagram'])->name('admin.diagrams.feature');
        Route::delete('/diagrams/{diagram}/feature', [AdminController::class, 'unfeatureDiagram'])->name('admin.diagrams.unfeature');
        Route::post('/users/{user}/email', [AdminController::class, 'sendEmail'])->name('admin.users.email');
        Route::get('/users/{user}/activity', [AdminController::class, 'userActivity'])->name('admin.users.activity');
        Route::post('/email-all', [AdminController::class, 'sendEmailToAll'])->name('admin.email-all');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
});

Route::prefix('/auth')->where(['driver' => 'google|github|gitlab'])->group(function () {
    Route::get('/{driver}', [AuthController::class, 'oauthRedirect']);
    Route::get('/{driver}/callback', [AuthController::class, 'oauthCallback']);
});

Route::get('/{any}', function ($any) {
    $exactRoutes = ['register', 'login', 'logout', 'verify-email', 'demo', 'diagrams', 'auth/callback'];
    $prefixRoutes = ['diagrams/', 'shared/', 'embed/', 'auth/'];

    if (in_array($any, $exactRoutes)) {
        return view('layouts.app');
    }
    foreach ($prefixRoutes as $prefix) {
        if (str_starts_with($any, $prefix)) {
            return view('layouts.app');
        }
    }
    abort(404);
})->where('any', '.*');
