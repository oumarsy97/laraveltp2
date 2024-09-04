<?php

namespace App\Services\Contracts;

interface EmailServiceInterface
{
    public function sendEmailWithAttachment(string $to, string $subject, string $body, string $attachmentPath): void;
}
