<?php

namespace App\Listeners;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Honeypot\Events\SpamDetectedEvent ;

class CheckHoneypotIsWorking
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SpamDetectedEvent $event): void
    {
        ray($event);
    }
}