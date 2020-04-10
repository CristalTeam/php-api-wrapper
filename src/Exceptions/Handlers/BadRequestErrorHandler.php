<?php

namespace Cristal\ApiWrapper\Exceptions\Handlers;

use Cristal\ApiWrapper\Exceptions\ApiBadRequestException;
use Cristal\ApiWrapper\Exceptions\ApiException;

/**
 * Class BadRequestErrorHandler
 * @package Cristal\ApiWrapper\Exceptions
 */
class BadRequestErrorHandler extends AbstractErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(ApiException $exception, array $requestArguments)
    {
        throw new ApiBadRequestException(
            $exception->getResponse(),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getPrevious()
        );
    }
}
