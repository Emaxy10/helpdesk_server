<?php

use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/agent', [UserController::class, 'agents']);

Route::get('/ticket', [TicketController::class, 'index']);
Route::post('/ticket/create', [TicketController::class, 'store']);
Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
Route::get('/ticket/{ticket}', [TicketController::class, 'show']);

Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy']);