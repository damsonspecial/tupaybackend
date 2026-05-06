<?php

namespace App\Listeners;

use App\Events\SettlementProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSettlementNotification implements ShouldQueue
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
    public function handle(SettlementProcessed $event): void
    {
        $transaction = $event->transaction;
        $user = $transaction->user;

        // Mocking a notification (Email/SMS/Push)
        Log::info("Notification sent to {$user->email}: Your settlement of {$transaction->amount} has been processed. Ref: {$transaction->reference}");
    }
}
