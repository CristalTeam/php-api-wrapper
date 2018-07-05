<?php

namespace Starif\ApiWrapper\Relations;

use Starif\ApiWrapper\Builder;
use Starif\ApiWrapper\Model;
use Starif\ApiWrapper\Relation;

class HasMany extends Relation
{
    protected $foreignKey;
    protected $localKey;


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
        $this->builder = $this->related->where($this->localKey, $this->parent->{$this->foreignKey});
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
}
