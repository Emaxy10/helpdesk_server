<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole($roles){
        if(is_array($roles)){
            return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
        }
         if(is_string($roles)){
            return $this->roles->contains('name', $roles);
        }

        return false;
    }

    public function assignRole($role){
            // If $role is a string, find the Role model by name
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        // If role doesn't exist, return false or throw an exception
        if (!$role) {
            return false;
        }

        // Attach the role to the user if not already assigned
        if (!$this->roles->contains($role->id)) {
            $this->roles()->attach($role);
        }

        return $this;
    }

    public function createdTickets() {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets() {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketComments()
    {
        return $this->hasMany(TicketComment::class);
    }

}
