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
        Route::get('/{id}', [DiagramController::class, 'show']);
        Route::post('/', [DiagramController::class, 'store']);
        Route::put('/{id}', [DiagramController::class, 'update']);
        Route::delete('/{id}', [DiagramController::class, 'destroy']);

        Route::group(["prefix" => "sql"], function () {
//            Route::post('/import/{id}', [DiagramController::class, 'import']);
            Route::get('/export/{id}', [DiagramController::class, 'export']);
        });
    });
});
