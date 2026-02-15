<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\Welcome;
use Illuminate\Support\Facades\Mail;

class Welcome_mailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $email;


    /**
     * Create a new job instance.
     */
    public function __construct($user, $email)
    {
        $this->user = $user;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    try {
        \Mail::to($this->email)->send(new \App\Mail\welcome($this->user));
    } catch (\Throwable $e) {
        \Log::error('Welcome mail failed', [
            'email' => $this->email,
            'error' => $e->getMessage(),
        ]);
    }
}
 
}
