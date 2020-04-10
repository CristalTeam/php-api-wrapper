<?php

namespace Cristal\ApiWrapper\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $response;

    public function __construct($response, $message = "", $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct("The request ended on a $httpCode code : $message", $httpCode, $previous);
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse($key = null)
    {
        return $key ? $this->response[$key] : $this->response;
    }

    /**
     * @return mixed
     */
    public function getApiMessage()
    {
        return $this->getResponse('message');
    }

    /**
     * @return mixed
     */
    public function getApiErrors()
    {
        return $this->getResponse('errors');
    }
}
