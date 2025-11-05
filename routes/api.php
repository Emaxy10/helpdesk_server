<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// --------------------
// Public routes
// --------------------
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// --------------------
// Sanctum CSRF cookie endpoint (handled by Sanctum automatically)
// --------------------
Route::get('/sanctum/csrf-cookie', function () {
    return response()->noContent();
});


// CSRF + session-based auth with Sanctum
Route::middleware(['web','auth:sanctum', 'role:admin,agent'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/agent', [UserController::class, 'agents']);

    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/ticket/create', [TicketController::class, 'store'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy']);
});
