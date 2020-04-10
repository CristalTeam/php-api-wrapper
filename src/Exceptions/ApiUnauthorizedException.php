<?php

namespace Cristal\ApiWrapper\Exceptions;

use Throwable;

class ApiUnauthorizedException extends ApiException
{
    public function __construct($response, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct($response, 'Unauthorized', $httpCode, $previous);
    }
}
