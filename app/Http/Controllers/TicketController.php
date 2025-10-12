<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Mail\TicketCreated;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Mail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    //
  public function store(StoreTicketRequest $request)
{
    $user = Auth::user();

    $paths = [];

    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $paths[] = $file->store('attachments', 'public');
        }
    }

    $ticket = Ticket::create([
        'title'       => $request->title,
        'description' => $request->description,
        'status'      => $request->status ?? 'open',
        'priority'    => $request->priority,
        'user_id'     => $user->id,
        'assigned_to' => $request->assigned_to,
        'attachment' => $paths ? json_encode($paths) : null,
    ]);

    //Send Mail
    Mail::to($user)->send(
        new TicketCreated($ticket)
    );

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

        } catch (Exception $e) {
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
     $tickets = Ticket::with('creator')->with('agent')->get();

        return response()->json(
            $tickets
        );
    }
 
   public function accept(Ticket $ticket, Request $request)
{
    $request->validate([
    'close_date' => 'required|date',
    ]);

    $user = Auth::user();

    try{

        if($ticket->agent->id !== $user->id){
            return response()->json([
            'message' => 'Unauthorized: Only the assigned agent can accept this ticket.'
        ], 403);
        }
        
        $ticket->update([
            'is_accepted' => 1,
            'status' => 'in-progress',
            'close_date' => $request->input('close_date'), // full timestamp
        ]);

        return response()->json([
            'message' => 'Ticket accepted successfully',
            'ticket'  => $ticket,
        ]);

    }catch(Exception $e){
         return response()->json([
                'message' => 'Failed to accept ticket',
                'error' => $e->getMessage()
            ], 500);
    }

    }


    public function myTickets(){
        $user = Auth::user();
        $tickets = $user->createdTickets()->with('agent')->get();

        return response()->json($tickets);

    }

    public function storeComments(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment'  => $validated['comment'],
        ]);

        
        // Load the user before returning
        $comment->load('user');

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }

    public function getComments(Ticket $ticket){
    $ticket->load('comments.user'); // loads comments with user info

        return response()->json([
            'ticket' => $ticket,
            'comments' => $ticket->comments,
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

    public function transfer(Ticket $ticket, Request $request){

        $user = Auth::user();

         $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try{

             if($ticket->agent->id !== $user->id){
                return response()->json([
                'message' => 'Unauthorized: Only the assigned agent can close this ticket.'
            ], 403);
            }

                $ticket->update([
                'assigned_to' => $request->input('assigned_to')
            ]);

            return response()->json([
                'message' => 'Ticket transfered successfully',
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => 'Failed to transfer ticket',
                'error' => $e->getMessage()
            ], 500);
        }

        
    }

    public function close(Ticket $ticket){
        $user = Auth::user();
        try{

            if($ticket->agent->id !== $user->id){
                return response()->json([
                'message' => 'Unauthorized: Only the assigned agent can close this ticket.'
            ], 403);
            }

            if($ticket->is_accepted === null){
                 return response()->json([
                'message' => 'Fail: Only the accepted ticket can be closed.'
                ], 403);
            }

            $ticket->update([
                'is_completed' => 1,
                'status' => 'closed'
            ]);

        }catch(Exception $e){
             return response()->json([
                'message' => 'Failed to close ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
