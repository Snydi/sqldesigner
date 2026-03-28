<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/shared/{token}', [DiagramController::class, 'showShared']);
Route::patch('/shared/{token}', [DiagramController::class, 'saveShared']);

Route::middleware(['auth:sanctum'])->group(function () {
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

        Route::group(['prefix' => 'sql'], function () {
            Route::post('/validate', [DiagramController::class, 'validateSQL']);
            Route::post('/import/{diagram}', [DiagramController::class, 'import']);
            Route::get('/export/{diagram}', [DiagramController::class, 'export']);
        });
    });
});
