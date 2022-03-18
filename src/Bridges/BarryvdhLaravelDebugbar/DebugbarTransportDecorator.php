<?php

namespace Cristal\ApiWrapper\Bridges\BarryvdhLaravelDebugbar;

use Barryvdh\Debugbar\LaravelDebugbar;
use Cristal\ApiWrapper\Exceptions\ApiException;
use Cristal\ApiWrapper\Transports\TransportInterface;
use DebugBar\DebugBarException;

/**
 * Class DebugbarTransportDecorator.
 */
class DebugbarTransportDecorator implements TransportInterface
{
    /**
     * @var string
     */
    const DEBUG_COLLECTOR = 'api';

    /**
     * @var TransportInterface
     */
    private $transport;

    /**
     * @var string
     */
    private $name;

    /**
     * @var LaravelDebugbar
     */
    private $debugbar;

    /**
     * DebugbarTransportDecorator constructor.
     *
     * @param TransportInterface $transport
     * @param string             $name
     * @param LaravelDebugbar    $debugbar
     */
    public function __construct(TransportInterface $transport, string $name, ?LaravelDebugbar $debugbar)
    {
        $this->transport = $transport;
        $this->name = $name;
        $this->debugbar = $debugbar;
    }

    /**
     * @return LaravelDebugbar
     */
    public function getDebugbar(): LaravelDebugbar
    {
        return $this->debugbar;
    }

    /**
     * Create a request and return the raw response.
     *
     * @param $endpoint
     * @param array  $data
     * @param string $method
     *
     * @return mixed
     */
    public function rawRequest($endpoint, array $data = [], $method = 'get')
    {
        return $this->getTransport()->rawRequest($endpoint, $data, $method);
    }

    /**
     * @return TransportInterface
     */
    public function getTransport(): TransportInterface
    {
        return $this->transport;
    }

    /**
     * Call rawRequest and handle the result.
     *
     * @param $endpoint
     * @param array  $data
     * @param string $method
     *
     * @return mixed
     *
     * @throws DebugBarException
     * @throws ApiException
     */
    public function request($endpoint, array $data = [], $method = 'get')
    {
        if (optional($this->getDebugbar())->isEnabled()) {
            $method = strtoupper($method);
            $start = $this->startMesure($method);

            try {
                $response = $this->getTransport()->request($endpoint, $data, $method);
            } catch (ApiException $exception) {
                $this->stopMesure($start, $endpoint, null, $data, $method, $exception);

                throw $exception;
            }

            $this->stopMesure($start, $endpoint, $response, $data, $method);

            return $response;
        }

        return $this->getTransport()->request($endpoint, $data, $method);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $method
     *
     * @return mixed
     */
    private function startMesure(string $method)
    {
        $start = microtime(true);
        start_measure(
            $this->getName(),
            sprintf('Temps d\'appel %s [%s]', $this->getName(), $method)
        );

        return $start;
    }

    /**
     * @param $start
     * @param $response
     * @param $endpoint
     * @param array             $data
     * @param string            $method
     * @param ApiException|null $exception
     *
     * @return DebugbarTransportDecorator
     *
     * @throws DebugBarException
     */
    private function stopMesure($start, $endpoint, $response = null, array $data = [], string $method = 'get', ?ApiException $exception = null): self
    {
        stop_measure($this->getName());

        if (!$response) {
            $response = $exception ? $exception->getResponse() : null;
        }

        $this->getDebugbar()
            ->getCollector(self::DEBUG_COLLECTOR)
            ->addMessage(
                (object) array_filter([
                    'data_request' => $data,
                    'response' => $response,
                    'exception' => $exception,
                ]),
                sprintf(
                    '[%s] %s[%s]',
                    $method,
                    $endpoint,
                    $this->formatDuration(microtime(true) - $start)
                )
            );

        return $this;
    }

    public static function formatDuration($seconds)
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        }

        if ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }

        return round($seconds, 2) . 's';
    }

    public function getResponseHeaders(): array
    {
        return $this->transport->getResponseHeaders();
    }
}
