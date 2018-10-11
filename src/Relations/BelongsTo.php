<?php

namespace Cpro\ApiWrapper\Relations;

use Cpro\ApiWrapper\Model;

class BelongsTo extends Relation
{
    protected $foreignKey;
    protected $localKey;

    public function __construct(Model $parent, Model $related, $foreignKey, $localKey)
    {
        parent::__construct($parent);
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        $this->addConstraints();
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        if (!($queryValue = $this->parent->{$this->foreignKey})) {
            return null;
        }

        return $this->builder->find($queryValue);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->builder = $this->related->newQuery();
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

        return new $class($data, isset($data[$this->localKey]));
    }
}
