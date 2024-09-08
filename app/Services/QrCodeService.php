<?php
// app/Services/QrCodeService.php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateQrCode($data)
    {
        return QrCode::size(200)->generate($data);
    }
}
