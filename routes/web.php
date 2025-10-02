<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// CSRF protection
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent();
});

// CSRF + session-based auth with Sanctum
Route::middleware('web')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/agent', [UserController::class, 'agents']);

    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/ticket/create', [TicketController::class, 'store'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/accept', [TicketController::class, 'accept']);
});
