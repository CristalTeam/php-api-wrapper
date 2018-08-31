<?php

namespace Cpro\ApiWrapper\Exceptions;

use Exception;

class MissingApiException extends Exception
{
    public function __construct($message = "Aucune API n'a été définie pour le modèle")
    {
        return parent::__construct($message);
    }
}
