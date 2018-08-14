<?php

namespace Cpro\ApiWrapper\Concerns;

trait HasCache
{
    /**
     * Caching data
     * @var array
     */
    protected static $cache = [];

    /**
     * Has cache
     * @param $key
     * @return bool
     */
    protected function hasCache($key)
    {
        return isset(static::$cache[$key]);
    }

    /**
     * Set cache
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function setCache($key, $value)
    {
        return static::$cache[$key] = $value;
    }

    /**
     * Get cached data
     *
     * @param $key
     * @return mixed
     */
    protected function getCache($key)
    {
        return isset(static::$cache[$key]) ? static::$cache[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function deleteCache($key)
    {
        unset(static::$cache[$key]);
        return true;
    }
}