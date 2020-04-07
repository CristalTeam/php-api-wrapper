<?php

namespace Cristal\ApiWrapper\Bridges\Laravel\Relations\Decorators;

use Cristal\ApiWrapper\Relations\Decorators\RelationWithoutBuilder as CoreRelationWithoutBuilder;
use Cristal\ApiWrapper\Relations\HasMany;
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
