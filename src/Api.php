<?php

namespace Starif\ApiWrapper;

/**
 * Class Api
 * @package Starif\ApiWrapper
 * @throws ApiException
 */
class Api
{
    /**
     * @var TransportInterface
     */
    private $transport;

    /**
     * Api constructor.
     *
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Get current instance of TransportInterface.
     *
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Magical use for getObjects(), getObject(), createObject(), updateObject(), deleteObject().
     *
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

        preg_match('/^(get|create|update|delete)([\w-]+?)(s|)$/', $name, $matches);

        $endpoint = strtolower($matches[2]);
        if ($matches[1] === 'get') {
            if ($matches[3] === 's') {
                return $this->findAll($endpoint, ...$arguments);
            }

            return $this->findOne($endpoint, ...$arguments);
        }

        return $this->{$matches[1]}($endpoint, ...$arguments);
    }

    /**
     * Call API for find all results of the entrypoint.
     *
     * @param string $endpoint
     * @param array  $filters
     * @return array
     */
    protected function findAll(string $endpoint, array $filters = []): array
    {
        return $this->transport->request('/'.$endpoint.'s', $filters);
    }

    /**
     * Call API for find entity of entrypoint.
     *
     * @param string $endpoint
     * @param int    $id
     * @return array
     */
    protected function findOne(string $endpoint, int $id): array
    {
        return $this->transport->request('/'.$endpoint.'/'.$id);
    }

    /**
     * Call API for update an entity.
     *
     * @param string $endpoint
     * @param int    $id
     * @param        $attributes
     * @return array
     */
    protected function update(string $endpoint, int $id, $attributes): array
    {
        return $this->transport->request('/'.$endpoint.'/'.$id, $attributes, 'put');
    }

    /**
     * Call API for create an entity.
     *
     * @param string $endpoint
     * @param        $attributes
     * @return array
     */
    protected function create(string $endpoint, $attributes): array
    {
        return $this->transport->request('/'.$endpoint, $attributes, 'post');
    }

    /**
     * Call API for delete an entity.
     *
     * @param string $endpoint
     * @param int    $id
     * @return array
     */
    protected function delete(string $endpoint, int $id): array
    {
        return $this->transport->request('/'.$endpoint.'/'.$id, [], 'delete');
    }
}
