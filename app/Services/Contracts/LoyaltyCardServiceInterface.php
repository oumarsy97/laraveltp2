<?php

namespace App\Services\Contracts;

interface LoyaltyCardServiceInterface
{
    public function sendLoyaltyCard($use,$pdf);
}
