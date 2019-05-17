<?php

namespace Cpro\ApiWrapper\Exceptions\Handlers;

use Cpro\ApiWrapper\Exceptions\ApiException;
use Cpro\ApiWrapper\Transports\TransportInterface;

/**
 * Class AbstractErrorHandler
 * @package Cpro\ApiWrapper\Exceptions
 */
abstract class AbstractErrorHandler
{
    /**
     * @var int
     */
    public $tries = 0;

    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var int
     */
    protected $maxTries = 3;

    /**
     * AbstractErrorHandler constructor.
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return int
     */
    public function getMaxTries(): int
    {
        return $this->maxTries;
    }

    /**
     * @param int $maxTries
     *
     * @return AbstractErrorHandler
     */
    public function setMaxTries(int $maxTries): self
    {
        $this->maxTries = $maxTries;

        return $this;
    }

    /**
     * @param ApiException $exception
     * @param array $requestArguments
     *
     * @return mixed
     *
     * @throws ApiException
     */
    abstract public function handle(ApiException $exception, array $requestArguments);
}