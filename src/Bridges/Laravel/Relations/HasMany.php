<?php

namespace Cristal\ApiWrapper\Bridges\Laravel\Relations;

use Cristal\ApiWrapper\Model;
use Cristal\ApiWrapper\Relations\HasMany as CoreHasMany;
use Illuminate\Database\Eloquent\Model as ModelEloquent;
use LogicException;
use Illuminate\Support\Collection;

class HasMany extends CoreHasMany
{
    /**
     * {@inheritDoc}
     */
    public function __construct($parent, Model $related, $foreignKey, $localKey)
    {
        if (!($parent instanceof Model || $parent instanceof ModelEloquent)) {
            throw new LogicException('Parent must be a Model instance.');
        }

        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;

        $this->addConstraints();
    }

    public function getRelationsFromArray($data)
    {
        return new Collection(parent::getRelationsFromArray($data));
    }

    public function getResults()
    {
        return new Collection(parent::getResults());
    }
}
