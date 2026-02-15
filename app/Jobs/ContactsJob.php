<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\Contacts\NewContact;
use App\Models\User;

class ContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $contact;

    public function __construct($contact)
    {
        $this->contact  = $contact ;
    }

    public function handle(): void
    {
        $users = User::permission('show contact_us')->get();

        foreach($users as $user) {
            $user->notify(new NewContact($this->contact));
        }
    }
}
