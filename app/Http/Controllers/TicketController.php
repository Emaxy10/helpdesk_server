<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    //
    public function store(StoreTicketRequest $request){
        $ticket = Ticket::create($request->all());

        return response()->json([
            'message' => 'Ticket created succesfully'
        ]);
    }

    public function index(){
        $tickets = Ticket::all();

        return response()->json(
            $tickets
        );
    }
}
