<?php

namespace Starif\ApiWrapper\Relations;

use Starif\ApiWrapper\Model;
use Starif\ApiWrapper\Relation;

class HasOne extends Relation
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
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->related->find($this->localKey, $this->parent->{$this->foreignKey});
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
        return new $class($data, isset($data[$this->localKey]));
    }
}