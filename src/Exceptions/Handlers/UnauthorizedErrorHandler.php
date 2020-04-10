<?php

namespace Cristal\ApiWrapper\Exceptions\Handlers;

use Cristal\ApiWrapper\Exceptions\ApiException;
use Cristal\ApiWrapper\Exceptions\ApiUnauthorizedException;

/**
 * Class UnauthorizedErrorHandler
 * @package Cristal\ApiWrapper\Exceptions
 */
class UnauthorizedErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        throw new ApiUnauthorizedException(
            $exception->getResponse(),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getPrevious()
        );
    }
}
