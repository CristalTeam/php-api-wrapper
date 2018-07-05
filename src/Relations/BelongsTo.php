<?php

namespace Starif\ApiWrapper\Relations;

class BelongsTo extends HasOne
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->builder->first();
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
}