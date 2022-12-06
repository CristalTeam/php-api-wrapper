<?php

namespace Cristal\ApiWrapper\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $response;

    /**
     * @var string|null Can be used to specify from which API the exception has been thrown.
     */
    protected $source;

    public function __construct($response, $message = "", $httpCode = 0, Throwable $previous = null)
    {
        parent::__construct($message, $httpCode, $previous);
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

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }
}
