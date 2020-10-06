<?php

namespace Cristal\ApiWrapper\Relations;

use Cristal\ApiWrapper\Model;

class HasMany extends Relation
{
    protected $foreignKey;
    protected $localKey;
    protected $queryKey;
    protected $queryValue;

    public function __construct(Model $parent, Model $related, $foreignKey, $localKey)
    {
        parent::__construct($parent);
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        $this->addConstraints();
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->queryKey = $this->localKey;
        $this->queryValue = $this->parent->{$this->foreignKey};

        $this->builder = $this->related->where($this->queryKey, $this->queryValue);
    }

    /**
     * Get the models corresponding to data passed by array.
     *
     * @param $data
     *
     * @return mixed
     */
    public function getRelationsFromArray($data)
    {
        $class = get_class($this->related);

        return array_map(function ($item) use ($class) {
            return new $class($item, isset($item[$this->localKey]));
        }, $data);
    }

    public function getResults()
    {
        $results = parent::getResults();

        if (null === $results) {
            return [];
        }

        return $results;
    }
}
