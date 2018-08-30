<?php

namespace Cpro\ApiWrapper\Relations;

use Cpro\ApiWrapper\Model;

abstract class Relation
{
    /**
     * The parent model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $related;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Create a new relation instance.
     *
     * @param Model $parent
     *
     * @return void
     */
    public function __construct(Model $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->builder->get();
    }

    /**
     * Set the base constraints on the relation query
     * Set builder.
     *
     * @return void
     */
    abstract public function addConstraints();

    /**
     * Get the models corresponding to data passed by array.
     *
     * @param $data
     *
     * @return mixed
     */
    abstract public function getRelationsFromArray($data);

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = $this->builder->{$method}(...$parameters);

        if ($result === $this->builder) {
            return $this;
        }

        return $result;
    }
}
