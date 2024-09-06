<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LoyaltyCardFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'loyaltyCard';
    }
}
