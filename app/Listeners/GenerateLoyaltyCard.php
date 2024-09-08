<?php
// app/Listeners/GenerateLoyaltyCard.php

namespace App\Listeners;

use App\Events\LoyaltyCardRequested;
use App\Jobs\GenerateAndSendLoyaltyCardJob;

class GenerateLoyaltyCard
{
    public function handle(LoyaltyCardRequested $event)
    {
        GenerateAndSendLoyaltyCardJob::dispatch($event->user);
    }
}
