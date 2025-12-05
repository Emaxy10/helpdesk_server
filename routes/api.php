<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// --------------------
// Public routes
// --------------------
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);



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




Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
     $user =$request->user();
            $user->load('roles');
            return $user;  
});
 
// CSRF + session-based auth with Sanctum

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/agent', [UserController::class, 'agents']);

    Route::post('/ticket/create', [TicketController::class, 'store']);

    Route::get('/ticket/my', [TicketController::class, 'myTickets']);

    Route::get('/tickets/{ticket}/comments', [TicketController::class, 'getComments']);

    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'storeComments']);
});


Route::middleware(['auth:sanctum', 'role:admin,agent'])->group(function () {

    // Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/ticket', [TicketController::class, 'index']);
    Route::patch('/ticket/{ticket}/accept', [TicketController::class, 'accept']);
   
    Route::patch('/ticket/{ticket}/transfer', [TicketController::class, 'transfer']);
    Route::patch('/ticket/{ticket}/close', [TicketController::class, 'close']);

    Route::patch('/ticket/{ticket}/update', [TicketController::class, 'update']);
    Route::get('/ticket/{ticket}', [TicketController::class, 'show']);
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy']);
});



