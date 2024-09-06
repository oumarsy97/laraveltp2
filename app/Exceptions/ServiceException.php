<?php

namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{
    //
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message = "Le service n'est pas accessible. Veuillez contacter l'administrateur du Service ", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
