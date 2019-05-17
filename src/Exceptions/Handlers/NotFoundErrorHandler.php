<?php

namespace Cpro\ApiWrapper\Exceptions\Handlers;

use Cpro\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cpro\ApiWrapper\Exceptions\ApiException;

/**
 * Class NetworkErrorHandler
 * @package Cpro\ApiWrapper\Exceptions
 */
class NotFoundErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        throw new ApiEntityNotFoundException($exception->getResponse(), $exception->getCode(), $exception->getPrevious());
    }
}