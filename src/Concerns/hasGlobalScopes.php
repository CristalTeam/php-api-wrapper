<?php

namespace Starif\ApiWrapper\Concerns;

trait hasGlobalScopes
{
    /**
     * Register a new global scope on the model.
     *
     * @param  array|string  $scope
     * @param  array|null  $implementation
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function addGlobalScope($scope, ?array $implementation = null)
    {
        if (is_string($scope) && ! is_null($implementation)) {
            return static::$globalScopes[static::class][$scope] = $implementation;
        } elseif (is_array($scope)) {
            return static::$globalScopes[static::class][spl_object_hash((object) $scope)] = $scope;
        }

        throw new \InvalidArgumentException('Global scope must be an array.');
    }

    /**
     * Determine if a model has a global scope.
     *
     * @param  array|string  $scope
     * @return bool
     */
    public static function hasGlobalScope($scope)
    {
        return ! is_null(static::getGlobalScope($scope));
    }

    /**
     * Get a global scope registered with the model.
     *
     * @param  string  $scope
     * @return array|null
     */
    public static function getGlobalScope($scope)
    {
        return static::$globalScopes[static::class][$scope];
    }

    /**
     * Get the global scopes for this class instance.
     *
     * @return array
     */
    public function getGlobalScopes()
    {
        return is_array(static::$globalScopes) && isset(static::$globalScopes[static::class]) ? static::$globalScopes[static::class] : [];
    }
}