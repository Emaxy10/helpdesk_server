<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //
     protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'assigned_to',
        'is_accepted',
        'close_date',
        'is_completed'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
{
    return $this->hasMany(TicketComment::class);
}

}
