<?php

namespace Cpro\ApiWrapper\Relations;

interface RelationInterface
{
    /**
     * Set the base constraints on the relation query
     * Set builder.
     *
     * @return void
     */
    public function addConstraints();

    /**
     * Get the models corresponding to data passed by array.
     *
     * @param $data
     *
     * @return mixed
     */
    public function getRelationsFromArray($data);

    /**
     * Set the base constraints on the relation query
     * Set builder.
     *
     * @return void
     */
    public function getResults();
}
