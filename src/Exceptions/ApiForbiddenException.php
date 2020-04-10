<?php

namespace Cristal\ApiWrapper\Exceptions;

use Throwable;

class ApiForbiddenException extends ApiException
{
    public function __construct($response, $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct($response, 'Forbidden', $httpCode, $previous);
    }
}
