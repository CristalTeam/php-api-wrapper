<?php

namespace Cristal\ApiWrapper\Exceptions;

use Exception;

class MissingApiException extends Exception
{
    public function __construct($message = "No API bound to the entity")
    {
        return parent::__construct($message);
    }
}
