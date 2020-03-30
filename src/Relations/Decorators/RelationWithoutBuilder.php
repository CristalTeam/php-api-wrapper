<?php

namespace Cpro\ApiWrapper\Relations\Decorators;

use Cpro\ApiWrapper\Relations\HasMany;
use Cpro\ApiWrapper\Relations\RelationInterface;

class RelationWithoutBuilder implements RelationInterface
{
    /**
     * @var RelationInterface
     */
    protected $relation;

    /**
     * @param RelationInterface $relation
     */
    public function __construct(RelationInterface $relation)
    {
        $this->relation = $relation;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->relation instanceof HasMany ? [] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraints()
    {
        return $this->relation->addConstraints();
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationsFromArray($data)
    {
        return $this->relation->getRelationsFromArray($data);
    }

    public function __call($name, $arguments)
    {
        return $this->relation->$name(...$arguments);
    }

    public function __set($name, $value)
    {
        $this->relation->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->relation->$name);
    }

    public function __get($name)
    {
        return $this->relation->$name;
    }

    public function __unset($name)
    {
        unset($this->relation->$name);
    }
}
