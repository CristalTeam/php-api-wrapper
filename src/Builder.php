<?php

namespace  Starif\ApiWrapper;

class Builder
{
    /**
     * @var array
     */
    protected $query = [];

    /**
     * The model being queried.
     *
     * @var Model
     */
    protected $model;

    /**
     * Get the underlying query builder instance.
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Applied global scopes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Removed global scopes.
     *
     * @var array
     */
    protected $removedScopes = [];

    /**
     * Set a model instance for the model being queried.
     *
     * @param  Model  $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model instance being queried.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function find($field, $value = null)
    {
        if (is_array($field)) {
            $this->query = array_merge($this->query, ['id' => $field]);
            return $this->where($this->query)->get();
        } elseif ($value !== null) {
            $this->query = array_merge($this->query, [$field => $value]);
            return $this->where($this->query)->get()[0] ?: null;
        }

        return $this->model->newInstance($this->model->getApi()->{'get'.ucfirst($this->model->getEntity())}($field, $this->query), true);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param      $field
     * @param null $value
     * @return self
     */
    public function where($field, $value = null)
    {
        if (!is_array($field)) {
            $field = [$field => $value];
        }

        $this->query = array_merge($this->query, $field);

        return $this;
    }

    /**
     * @return self[]
     */
    public function all()
    {
        return $this->get();
    }

    /**
     * Register a new global scope.
     *
     * @param  string  $identifier
     * @param  array  $scope
     * @return $this
     */
    public function withGlobalScope($identifier, array $scope)
    {
        $this->scopes[$identifier] = $scope;
        $this->where($scope);

        return $this;
    }

    /**
     * Apply the given scope on the current builder instance.
     *
     * @param  array  $scope
     * @param  array  $parameters
     * @return mixed
     */
    protected function callScope(array $scope, $parameters = [])
    {
        $result = $scope[0]->{$scope[1]}($this);

        return $result;
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->model, $scope = 'scope'.ucfirst($method))) {
            return $this->callScope([$this->model, $scope], $parameters);
        }

        $this->query->{$method}(...$parameters);

        return $this;
    }

    /**
     * Execute the query.
     *
     * @return array|static[]
     */
    public function get()
    {
        $builder = $this;

        $instance = $this->getModel();
        $entities = $instance->getApi()->{'get'.ucfirst($instance->getEntity()).'s'}($builder->getQuery());
        return array_map(function ($entity) {
            return $this->model->newInstance($entity, true);
        }, $entities['data']);
    }
}