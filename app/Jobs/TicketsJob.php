<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\Tickets\UserRepliedToTicket;
use App\Notifications\Tickets\NewTicket;
use App\Notifications\Tickets\NewTicketByAdmin;
use App\Notifications\Tickets\AdminRepliedToTicket;

class TicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $ticket;
    public $type;

    public function __construct($users, $ticket, $type)
    {
        $this->users  = $users ;
        $this->ticket  = $ticket ;
        $this->type  = $type ;
    }

    public function handle(): void
    {
        if ($this->type == 'create'){
            foreach($this->users as $user) {
                $user->notify(new NewTicket($this->ticket));
            }
        } elseif ($this->type == 'user reply'){
            foreach($this->users as $user) {
                $user->notify(new UserRepliedToTicket($this->ticket));
            }
        } elseif ($this->type == 'admin reply'){
            $this->users->notify(new AdminRepliedToTicket($this->ticket));
        } elseif ($this->type == 'admin_create'){
            $this->users->notify(new NewTicketByAdmin($this->ticket));
        }
    }
}
