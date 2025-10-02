<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    //
  public function store(StoreTicketRequest $request)
{
    $user_id = Auth::id();

    $ticket = Ticket::create([
        'title'       => $request->title,
        'description' => $request->description,
        'status'      => $request->status ?? 'open',
        'priority'    => $request->priority,
        'user_id'     => $user_id,
        'assigned_to' => $request->assigned_to,
    ]);

    return response()->json([
        'ticket'  => $ticket,
        'message' => 'Ticket created successfully'
    ], 201);
}


    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        try {
            // Update ticket 
            $ticket->update($request->validated());

            return response()->json([
                'message' => 'Ticket updated successfully',
                'ticket' => $ticket
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Ticket $ticket){
        return response()->json($ticket);
    }


    public function index(){
     $tickets = Ticket::with('creator')->get();

        return response()->json(
            $tickets
        );
    }
 
   public function accept(Ticket $ticket, Request $request)
{
    $request->validate([
    'close_date' => 'required|date|after_or_equal:now',
    ]);


    $ticket->update([
        'is_accepted' => 1,
        'close_date' => $request->input('close_date'), // full timestamp
    ]);

    return response()->json([
        'message' => 'Ticket accepted successfully',
        'ticket'  => $ticket,
    ]);
}

    public function destroy(Ticket $ticket){
        try{
             $ticket->delete();
        return response()->json(['message' => 'ticket deleted successfully']);
        }catch(Exception $e){
            return response()->json([
                'message' => 'Failed to delete ticket',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
}
