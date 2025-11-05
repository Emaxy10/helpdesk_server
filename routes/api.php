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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
     $user =$request->user();
            $user->load('roles');
            return $user;  
});


// CSRF + session-based auth with Sanctum
Route::middleware(['auth:sanctum', 'role:admin,agent'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/agent', [UserController::class, 'agents']);
     Route::get('/ticket/my', [TicketController::class, 'myTickets']);

    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/ticket/create', [TicketController::class, 'store'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy']);
});



