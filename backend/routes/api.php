<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagramChangelogController;
use App\Http\Controllers\DiagramController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware('throttle:5,1')->post('/feedback', [FeedbackController::class, 'send']);

Route::get('/diagrams/embed/{token}', [DiagramController::class, 'showEmbed']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/diagrams/shared/{token}', [DiagramController::class, 'showByToken']);
    Route::patch('/diagrams/shared/{token}', [DiagramController::class, 'saveByToken']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    Route::group(["prefix" => "diagrams", "middleware" => ["verified"]], function () {
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

        Route::get('/{diagram}/changelog', [DiagramChangelogController::class, 'index']);
        Route::post('/{diagram}/changelog', [DiagramChangelogController::class, 'store']);
    });
});
