<?php
// Ticket Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    protected $fillable = [
        'subject',
        'support_type',
        'description',
        'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketMessages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
