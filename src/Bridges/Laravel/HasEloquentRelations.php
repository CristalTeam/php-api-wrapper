<?php

namespace Cristal\ApiWrapper\Bridges\Laravel;

use Cristal\ApiWrapper\Relations\RelationInterface;
use Cristal\ApiWrapper\Bridges\Laravel\Relations\HasOne as BridgeHasOne;
use Cristal\ApiWrapper\Bridges\Laravel\Relations\HasMany as BridgeHasMany;
use Cristal\ApiWrapper\Bridges\Laravel\Relations\BelongsTo as BridgeBelongsTo;
use Cristal\ApiWrapper\Bridges\Laravel\Relations\Builder as BridgeBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;

trait HasEloquentRelations
{
    /**
     * Define a one-to-many relationship.
     *
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return BridgeHasMany|HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        // Support Eloquent relations.
        if ($instance instanceof Eloquent) {
            return new HasMany($instance->newQuery(), $this->createFakeEloquentModel(), $foreignKey, $localKey);
        }

        return new BridgeHasMany($this, $instance, $foreignKey, $localKey);
    }

    /**
     * @param string $related
     * @param null $foreignKey
     * @param null $localKey
     *
     * @return BridgeHasOne|HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        // Support Eloquent relations.
        if ($instance instanceof Eloquent) {
            return new HasOne($instance->newQuery(), $this->createFakeEloquentModel(), $foreignKey, $localKey);
        }

        return new BridgeHasOne($this, $instance, $foreignKey, $localKey);
    }


    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relationName = null)
    {
        $parent = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $parent->getForeignKey();
        $ownerKey = $ownerKey ?: $parent->getKeyName();

        if ($parent instanceof Eloquent) {
            return new BelongsTo($parent->newQuery(), $this->createFakeEloquentModel(), $foreignKey, $ownerKey, $relationName);
        }

        return new BridgeBelongsTo($parent, $this, $foreignKey, $ownerKey);
    }

    /**
     * @return BridgeBuilder
     */
    public function newBuilder()
    {
        return new BridgeBuilder();
    }

    /**
     * @return bool Returns true.
     */
    public function push()
    {
        return true;
    }

    /**
     * Proxy for getEntity method.
     *
     * @return string|null
     */
    public static function getTableName()
    {
        return (new static())->getEntity();
    }

    /**
     * Create anonymous Eloquent model filled with current attributes.
     *
     * @return Eloquent
     */
    protected function createFakeEloquentModel()
    {
        $fakeModel = new class() extends Eloquent
        {
        };
        $fakeModel->exists = true;
        $fakeModel->forceFill($this->getAttributes());

        return $fakeModel;
    }

    /**
     * Get a relationship value from a method.
     *
     * @param string $method
     *
     * @return mixed
     *
     * @throws LogicException
     */
    protected function getRelationshipFromMethod($method)
    {
        $relation = $this->$method();

        if (!$relation instanceof RelationInterface && !$relation instanceof Relation) {
            throw new LogicException(__METHOD__ . ' must return a relationship instance.');
        }

        $results = $relation->getResults();
        $this->setRelation($method, $results);

        return $results;
    }
}
