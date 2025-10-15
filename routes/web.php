<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


Route::get('/attachments/{filename}', function ($filename, Request $request) {
    $path = 'attachments/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->download(storage_path('app/public/' . $path));
});


//Mail

Route::get('/test', function(){
    // return new TicketCreated(); -> See the message in the mail


    // Mail::to('chigozieiroawula@outlook.com')->send(
    //     new TicketCreated()
    // );-> Send message with mail::to

    // return 'done';

});

// CSRF protection
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent();
});

// CSRF + session-based auth with Sanctum
Route::middleware('web')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/agent', [UserController::class, 'agents']);
    Route::get('/ticket/my', [TicketController::class, 'myTickets'])->middleware('auth:sanctum');

    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/ticket/create', [TicketController::class, 'store'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/accept', [TicketController::class, 'accept']);
    Route::get('/tickets/{ticket}/comments', [TicketController::class, 'getComments'])->middleware('auth:sanctum');
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'storeComments'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/transfer', [TicketController::class, 'transfer'])->middleware('auth:sanctum');
    Route::patch('/ticket/{ticket}/close', [TicketController::class, 'close'])->middleware('auth:sanctum');

    
});
