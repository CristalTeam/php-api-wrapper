<?php

namespace Cpro\ApiWrapper\Bridges\Laravel;

use Cpro\ApiWrapper\Relations\RelationInterface;
use Cpro\ApiWrapper\Bridges\Laravel\Relations\HasOne as BridgeHasOne;
use Cpro\ApiWrapper\Bridges\Laravel\Relations\HasMany as BridgeHasMany;
use Cpro\ApiWrapper\Bridges\Laravel\Relations\Builder as BridgeBuilder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
