<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagramChangelogController;
use App\Http\Controllers\DiagramController;
use App\Http\Controllers\PlanLimitController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RobokassaWebhookController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/stats', [StatsController::class, 'index'])->middleware('throttle:60,1');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware('throttle:5,1')->post('/support', [SupportController::class, 'send']);

Route::get('/diagrams/embed/{token}', [DiagramController::class, 'showEmbed']);
Route::middleware('throttle:60,1')->post('/webhooks/robokassa/result', [RobokassaWebhookController::class, 'result']);

Route::middleware(['auth:sanctum', 'track.seen'])->group(function () {
    Route::get('/diagrams/shared/{token}', [DiagramController::class, 'showByToken']);
    Route::patch('/diagrams/shared/{token}', [DiagramController::class, 'saveByToken']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    Route::get('/plan-limits', [PlanLimitController::class, 'show']);
    Route::middleware('throttle:5,1')->post('/subscription/checkout', [SubscriptionController::class, 'checkout']);
    Route::get('/subscription/me', [SubscriptionController::class, 'me']);
    Route::delete('/subscription/current', [SubscriptionController::class, 'cancel']);
    Route::get('/review', [ReviewController::class, 'check']);
    Route::middleware('throttle:5,1')->post('/review', [ReviewController::class, 'store']);

    Route::group(['prefix' => 'diagrams', 'middleware' => ['verified']], function () {
        Route::get('/', [DiagramController::class, 'index']);
        Route::get('/{diagram}', [DiagramController::class, 'show']);
        Route::post('/', [DiagramController::class, 'store']);
        Route::put('/{diagram}', [DiagramController::class, 'update']);
        Route::delete('/{diagram}', [DiagramController::class, 'destroy']);

        Route::post('/{diagram}/share', [DiagramController::class, 'share']);
        Route::delete('/{diagram}/share', [DiagramController::class, 'unshare']);
        Route::patch('/{diagram}/share', [DiagramController::class, 'updateShareAccess']);
        Route::get('/{diagram}/visitors', [DiagramController::class, 'getVisitors']);
        Route::post('/{diagram}/visitors/{visitor}/approve', [DiagramController::class, 'approveVisitor']);
        Route::patch('/{diagram}/visitors/{visitor}', [DiagramController::class, 'updateVisitorAccess']);

        Route::group(['prefix' => 'sql'], function () {
            Route::post('/import/{diagram}', [DiagramController::class, 'import']);
            Route::get('/import-status/{diagram}', [DiagramController::class, 'importStatus']);
            Route::post('/export/{diagram}', [DiagramController::class, 'export']);
            Route::get('/export-status/{diagram}', [DiagramController::class, 'exportStatus']);
        });

        Route::get('/json/export/{diagram}', [DiagramController::class, 'exportJson']);
        Route::get('/migration/export/{diagram}', [DiagramController::class, 'exportMigration']);
        Route::post('/png/export/{diagram}', [DiagramController::class, 'recordPngExport']);

        Route::get('/{diagram}/changelog', [DiagramChangelogController::class, 'index']);
        Route::post('/{diagram}/changelog', [DiagramChangelogController::class, 'store']);
    });
});
