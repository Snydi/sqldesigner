<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagramController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::group(["prefix" => "diagrams"], function () {
        Route::get('/', [DiagramController::class, 'index']);
        Route::get('/{diagram}', [DiagramController::class, 'show']);
        Route::post('/', [DiagramController::class, 'store']);
        Route::put('/{diagram}', [DiagramController::class, 'update']);
        Route::delete('/{diagram}', [DiagramController::class, 'destroy']);

        Route::group(['prefix' => 'sql'], function () {
            Route::post('/validate', [DiagramController::class, 'validateSQL']);
            Route::post('/import/{diagram}', [DiagramController::class, 'import']);
            Route::get('/export/{diagram}', [DiagramController::class, 'export']);
        });
    });
});
