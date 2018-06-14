<?php

namespace Starif\ApiWrapper\Relations;

use Starif\ApiWrapper\Builder;
use Starif\ApiWrapper\Model;
use Starif\ApiWrapper\Relation;

class HasMany extends Relation
{
    protected $foreignKey;
    protected $localKey;

    /**
     * @var Builder
     */
    protected $builder;

    public function __construct(Model $parent, Model $related, $foreignKey, $localKey)
    {
        parent::__construct($parent);
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->builder = $this->related->newBuilder();
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->builder->where($this->localKey, $this->parent->{$this->foreignKey})->get();
    }

    /**
     * Get the models corresponding to data passed by array.
     *
     * @param $data
     * @return mixed
     */
    public function getRelationsFromArray($data)
    {
        $class = get_class($this->related);
        return array_map(function($item) use ($class){
            return new $class($item, isset($item[$this->localKey]));
        }, $data);
    }



    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param  string  $method
     * @param  array   $parameters
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