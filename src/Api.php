<?php

namespace Cristal\ApiWrapper;

use Cristal\ApiWrapper\Concerns\HasCache;
use Cristal\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cristal\ApiWrapper\Transports\TransportInterface;

/**
 * Class Api.
 */
class Api
{
    use HasCache;

    /**
     * @var TransportInterface
     */
    protected $transport;

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
     *
     * @return array
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

        preg_match('/^(get|create|update|delete)([\w\-_\/]+?)$/', $name, $matches);

        $endpoint = strtolower($matches[2]);
        if ('get' === $matches[1]) {
            if (array_key_exists(0, $arguments) && !is_array($arguments[0])) {
                return $this->findOne($endpoint, ...$arguments);
            }

            return $this->findAll($endpoint, ...$arguments);
        }

        return $this->{$matches[1]}($endpoint, ...$arguments);
    }

    /**
     * Call API for find all results of the entrypoint.
     *
     * @param string $endpoint
     * @param array  $filters
     *
     * @return array
     */
    protected function findAll(string $endpoint, array $filters = []): array
    {
        $key = md5(__FUNCTION__.$endpoint.json_encode($filters));

        if ($this->hasCache($key)) {
            return $this->getCache($key);
        }

        return $this->setCache(
            $key,
            $this->getTransport()->request('/'.$endpoint, $filters) ?? []
        );
    }

    /**
     * Call API for find entity of entrypoint.
     *
     * @param string $endpoint
     * @param int    $id
     * @param array  $filters
     *
     * @return array
     */
    protected function findOne(string $endpoint, $id, array $filters = [])
    {
        // Makes no sense to proceed API call if we passed null id.
        // It would mean that we would like to do a findAll call instead and it could cause side effects.
        if ($id === null) {
            throw new ApiEntityNotFoundException([]);
        }

        $uri = '/'.$endpoint.'/'.$id;
        $key = $uri.'?'.http_build_query($filters);
        if ($this->hasCache($key)) {
            return $this->getCache($key);
        }

        return $this->getTransport()->request($uri, $filters) ?? [];
    }

    /**
     * Call API for update an entity.
     *
     * @param string $endpoint
     * @param int    $id
     * @param        $attributes
     *
     * @return mixed|array
     */
    protected function update(string $endpoint, $id, $attributes)
    {
        $key = $endpoint.'/'.$id.'?';

        return $this->setCache($key, $this->getTransport()->request('/'.$endpoint.'/'.$id, $attributes, 'put') ?? []);
    }

    /**
     * Call API for create an entity.
     *
     * @param string $endpoint
     * @param        $attributes
     *
     * @return mixed|array
     */
    protected function create(string $endpoint, $attributes)
    {
        return $this->getTransport()->request('/'.$endpoint, $attributes, 'post') ?? [];
    }

    /**
     * Call API for delete an entity.
     *
     * @param string $endpoint
     * @param int    $id
     *
     * @return mixed|array
     */
    protected function delete(string $endpoint, $id)
    {
        $key = $endpoint.'/'.$id.'?';
        $this->deleteCache($key);

        return $this->getTransport()->request('/'.$endpoint.'/'.$id, [], 'delete') ?? [];
    }
}
