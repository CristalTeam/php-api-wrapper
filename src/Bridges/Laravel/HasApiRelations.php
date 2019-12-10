<?php

namespace Cpro\ApiWrapper\Bridges\Laravel;

use Cpro\ApiWrapper\Bridges\Laravel\Relations\BelongsTo as ApiBelongsTo;
use Cpro\ApiWrapper\Bridges\Laravel\Relations\HasMany as ApiHasMany;
use Cpro\ApiWrapper\Bridges\Laravel\Relations\HasOne as ApiHasOne;
use Cpro\ApiWrapper\Model as ModelApi;
use Cpro\ApiWrapper\Relations\BelongsTo;
use Cpro\ApiWrapper\Relations\Relation as RelationApi;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;

trait HasApiRelations
{
    /**
     * {@inheritdoc}
     *
     * @return belongsTo|ApiBelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $instance = new $related();

        if ($instance instanceof ModelApi) {
            $foreignKey = $foreignKey ?: $instance->getForeignKey();

            $ownerKey = $ownerKey ?: $instance->getKeyName();

            return new ApiBelongsTo($this, $instance, $foreignKey, $ownerKey);
        }

        return parent::belongsTo($related, $foreignKey, $ownerKey, $relation);
    }

    /**
     * {@inheritdoc}
     *
     * @return HasOne|ApiHasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related();

        if ($instance instanceof ModelApi) {
            $foreignKey = $foreignKey ?: $this->getForeignKey();

            $localKey = $localKey ?: $this->getKeyName();

            return new ApiHasOne($this, $instance, $foreignKey, $localKey);
        }

        return parent::hasOne($related, $foreignKey, $localKey);
    }

    /**
     * {@inheritdoc}
     *
     * @return HasMany|ApiHasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related();
        if ($instance instanceof ModelApi) {
            $foreignKey = $foreignKey ?: $this->getForeignKey();

            $localKey = $localKey ?: $this->getKeyName();

            return new ApiHasMany($this, $instance, $foreignKey, $localKey);
        }

        return parent::hasMany($related, $foreignKey, $localKey);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipFromMethod($method)
    {
        $relation = $this->$method();

        if (!($relation instanceof Relation || $relation instanceof RelationApi)) {
            throw new LogicException(\get_class($this).'::'.$method.' must return a relationship instance.');
        }

        return tap($relation->getResults(), function ($results) use ($method) {
            $this->setRelation($method, $results);
        });
    }
}
