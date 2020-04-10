<?php

namespace Cristal\ApiWrapper\Exceptions\Handlers;

use Cristal\ApiWrapper\Exceptions\ApiException;
use Cristal\ApiWrapper\Exceptions\ApiForbiddenException;

/**
 * Class ForbiddenErrorHandler
 * @package Cristal\ApiWrapper\Exceptions
 */
class ForbiddenErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        throw new ApiForbiddenException(
            $exception->getResponse(),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getPrevious()
        );
    }
}
