<?php

namespace App\Services\Contracts;

interface LoyaltyCardServiceInterface
{
    public function sendLoyaltyCard($user,$pdf);
}
