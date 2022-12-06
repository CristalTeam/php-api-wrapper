<?php

namespace Cristal\ApiWrapper\Exceptions\Handlers;

use Cristal\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cristal\ApiWrapper\Exceptions\ApiException;

/**
 * Class NetworkErrorHandler
 * @package Cristal\ApiWrapper\Exceptions
 */
class NotFoundErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        throw new ApiEntityNotFoundException(
            $exception->getResponse(),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getPrevious()
        );
    }
}
