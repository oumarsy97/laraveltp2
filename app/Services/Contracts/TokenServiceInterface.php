<?php

// app/Services/TokenServiceInterface.php
namespace App\Services\Contracts;

interface TokenServiceInterface
{
    public function createToken($user);
    public function createRefreshToken($user);
}
