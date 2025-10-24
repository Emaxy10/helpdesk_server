<?php

use App\Models\Ticket;
use App\Mail\TicketCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;


Route::get('/attachments/{filename}', function ($filename, Request $request) {
    $path = 'attachments/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->download(storage_path('app/public/' . $path));
});


//Mail

Route::get('/test', function(){
    $ticket = Ticket::find(1);

    return new TicketCreated($ticket);

});

// CSRF protection
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent();
});

// Public routes
Route::middleware('web')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

//Authenticated
Route::middleware(['web', 'auth:sanctum', 'role:admin,agent,user'])->group(function(){
    Route::get('/agent', [UserController::class, 'agents']);
    Route::get('/ticket/my', [TicketController::class, 'myTickets']);
    Route::get('/user', function (Request $request) {

       $user =$request->user();
            $user->load('roles');
            return $user;    
    });
    Route::get('/ticket', [TicketController::class, 'index']);
    Route::post('/logout', [UserController::class, 'logout']);
      Route::post('/ticket/create', [TicketController::class, 'store']);
});

// CSRF + session-based auth with Sanctum
Route::middleware(['web','auth:sanctum', 'role:admin,agent'])->group(function () {
   
    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy']);
    Route::patch('/ticket/{ticket}/accept', [TicketController::class, 'accept']);
    Route::get('/tickets/{ticket}/comments', [TicketController::class, 'getComments']);
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'storeComments']);
    Route::patch('/ticket/{ticket}/transfer', [TicketController::class, 'transfer']);
    Route::patch('/ticket/{ticket}/close', [TicketController::class, 'close']);

    Route::get('/user/search/{search}',[UserController::class, 'search']);
    Route::post('/agent/add', [UserController::class, 'addAgent']);
    Route::delete('/agent/remove/{id}', [UserController::class, 'removeAgent']);

    
});
