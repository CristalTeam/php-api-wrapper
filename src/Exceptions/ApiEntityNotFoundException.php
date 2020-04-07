<?php

namespace Cristal\ApiWrapper\Exceptions;

use Throwable;

class ApiEntityNotFoundException extends ApiException
{
    public function __construct($response, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct(
            $response,
            "La requête à l'API a renvoyé une erreur $httpCode : L'entité demandée est introuvable.",
            $httpCode,
            $previous
        );
    }
}
