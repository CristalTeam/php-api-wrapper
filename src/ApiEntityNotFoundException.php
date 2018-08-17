<?php

namespace Cpro\ApiWrapper;

use Exception;
use Throwable;

class ApiEntityNotFoundException extends ApiException
{
    public function __construct($response, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct($response, $httpCode, $previous);
        $this->message = "La requête à l'API a renvoyé une erreur $httpCode : L'entité demandée est introuvable.";
    }
}
