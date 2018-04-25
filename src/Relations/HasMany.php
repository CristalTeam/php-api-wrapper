<?php

namespace Starif\ApiWrapper\Relations;

use Starif\ApiWrapper\Model;
use Starif\ApiWrapper\Relation;

class HasMany extends Relation
{
    private $foreignKey;
    private $localKey;

    public function __construct(Model $parent, Model $related, $foreignKey, $localKey)
    {
        parent::__construct($parent);
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
        return $this->related->where($this->foreignKey, $this->parent->{$this->foreignKey});
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
            return new $class($item, isset($item[$this->foreignKey]));
        }, $data);
    }
}