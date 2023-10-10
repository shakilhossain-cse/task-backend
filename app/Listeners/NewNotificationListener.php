<?php

namespace App\Listeners;

use App\Events\NewNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NewNotificationListener
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

     public function handle(NewNotificationEvent $event)
    {
        try {
            $notification = $event->notification;
            Log::info('New Notification:', ['data' => $notification->data]);
        } catch (\Exception $e) {
            Log::error('Error handling NewNotificationEvent: ' . $e->getMessage());
        }
    }
}
