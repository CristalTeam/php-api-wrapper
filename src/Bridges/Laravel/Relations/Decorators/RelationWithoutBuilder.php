<?php

namespace Cpro\ApiWrapper\Bridges\Laravel\Relations\Decorators;

use Cpro\ApiWrapper\Relations\Decorators\RelationWithoutBuilder as CoreRelationWithoutBuilder;
use Cpro\ApiWrapper\Relations\HasMany;
use Illuminate\Support\Collection;

class RelationWithoutBuilder extends CoreRelationWithoutBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->relation instanceof HasMany ? new Collection() : null;
    }
}
