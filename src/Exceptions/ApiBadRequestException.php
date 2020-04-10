<?php

namespace Cristal\ApiWrapper\Exceptions;

use Throwable;

class ApiBadRequestException extends ApiException
{
    public function __construct($response, $message, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct($response, $message, $httpCode, $previous);
    }
}
