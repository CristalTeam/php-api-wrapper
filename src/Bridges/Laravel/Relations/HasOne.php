<?php

namespace Cpro\ApiWrapper\Bridges\Laravel\Relations;

use Cpro\ApiWrapper\Model;
use Cpro\ApiWrapper\Relations\HasOne as CoreHasOne;
use Illuminate\Database\Eloquent\Model as ModelEloquent;
use LogicException;

class HasOne extends CoreHasOne
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
}
