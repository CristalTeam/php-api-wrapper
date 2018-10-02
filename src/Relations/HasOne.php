<?php

namespace Cpro\ApiWrapper\Relations;

class HasOne extends HasMany
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
