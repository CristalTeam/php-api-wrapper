<?php

namespace Cristal\ApiWrapper\Exceptions;

use Throwable;

class ApiEntityNotFoundException extends ApiException
{
    public function __construct($response, $message = null, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct(
            $response,
            $message ?? 'Entity not found',
            $httpCode,
            $previous
        );
    }
}
