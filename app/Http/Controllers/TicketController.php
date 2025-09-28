<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    //
    public function store(StoreTicketRequest $request){
        $ticket = Ticket::create($request->all());

        return response()->json([
            'ticket' => $ticket,
            'message' => 'Ticket created succesfully'
        ]);
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
        $tickets = Ticket::all();

        return response()->json(
            $tickets
        );
    }

    public function destroy(Ticket $ticket){
         $ticket->delete();
        return response()->json(['message' => 'ticket deleted successfully']);
    }
}
