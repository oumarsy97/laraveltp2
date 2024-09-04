<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\Contracts\QRCodeGeneratorInterface;
use App\Services\Contracts\LoyaltyCardServiceInterface;

class LoyaltyCardService implements LoyaltyCardServiceInterface
{



    public function sendLoyaltyCard($user, $pdf)
    {
        try{

            Mail::to($user->email)->send(new LoyaltyCardMail($user, $pdf));

        } catch (\Exception $e) {
            return new ServiceException("Erreur dans le service: " . $e->getMessage(), $e->getCode());
        }
    }
}
