<?php

namespace Cpro\ApiWrapper\Exceptions\Handlers;

use Cpro\ApiWrapper\Exceptions\ApiException;

/**
 * Class NetworkErrorHandler
 * @package Cpro\ApiWrapper\Exceptions
 */
class NetworkErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        if ($this->tries < $this->getMaxTries()) {
            $this->tries++;

            return $this->transport->request(...$requestArguments);
        }

        throw $exception;
    }
}