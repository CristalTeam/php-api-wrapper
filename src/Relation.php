<?php

namespace Starif\ApiWrapper;

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
     * Create a new relation instance.
     *
     * @param  Model  $parent
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
    abstract public function getResults();

    /**
     * Get the models corresponding to data passed by array.
     *
     * @param $data
     * @return mixed
     */
    abstract function getRelationsFromArray($data);
}