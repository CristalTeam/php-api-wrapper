<?php

namespace Cristal\ApiWrapper;

use ArrayAccess;
use Closure;
use Cristal\ApiWrapper\Concerns\HasAttributes;
use Cristal\ApiWrapper\Concerns\HasRelationships;
use Cristal\ApiWrapper\Concerns\HasGlobalScopes;
use Cristal\ApiWrapper\Concerns\HidesAttributes;
use Cristal\ApiWrapper\Exceptions\ApiException;
use Cristal\ApiWrapper\Exceptions\MissingApiException;
use Exception;
use JsonSerializable;

abstract class Model implements ArrayAccess, JsonSerializable
{
    use HasAttributes;
    use HasRelationships;
    use HasGlobalScopes;
    use HidesAttributes;

    /**
     * The entity model's name on Api.
     *
     * @var string
     */
    protected $entity;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Table resolver for different apis.
     *
     * @var array Apis
     */
    protected static $apis = [];

    /**
     * index for the api resolver.
     *
     * @var string api
     */
    protected static $api = 'default';

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The array of global scopes on the model.
     *
     * @var array
     */
    protected static $globalScopes = [];

    /**
     * Indicates if the model was inserted during the current request lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

    /**
     * Set the Model Api.
     *
     * @param Api|Closure $api
     */
    public static function setApi($api)
    {
        static::$apis[static::$api] = $api;
    }

    /**
     * Get the Model Api.
     *
     * @return Api
     */
    public function getApi(): Api
    {
        if (!static::$apis[static::$api] ?? null) {
            throw new MissingApiException();
        }

        $api = static::$apis[static::$api];

        if (is_callable($api)) {
            $api = $api();
        }

        if (!$api instanceof Api) {
            throw new MissingApiException();
        }

        return $api;
    }

    /**
     * @return string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getEntities(): string
    {
        if (substr($this->entity, -1) === 'y') {
            return rtrim($this->entity, 'y').'ies';
        }

        return rtrim($this->entity, 's').'s';
    }

    public function __construct($fill = [], $exists = false)
    {
        $this->exists = $exists;
        $this->fill($fill);
        $this->syncOriginal();
        $this->boot();
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public function boot()
    {
        static::bootMethods();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootMethods()
    {
        $class = static::class;
        foreach (preg_grep('/^boot[A-Z](\w+)/i', get_class_methods($class)) as $method) {
            if ($method === __FUNCTION__) {
                continue;
            }
            forward_static_call([$class, $method]);
        }
    }

    /**
     * @param array $attributes
     *
     * @return static
     *
     * @throws ApiException
     */
    public static function create(array $attributes = [])
    {
        $model = new static($attributes);
        $model->save();

        return $model;
    }

    /**
     * Fills the entry with the supplied attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes = [])
    {
        // start_measure('fill-model', 'Création de l\'objet '.static::class);
        foreach ($attributes as $key => $value) {
            if (is_array($value) && method_exists($this, $key)) {
                $this->setRelation($key,
                    $this->$key()->getRelationsFromArray($value)
                );
            } else {
                $this->setAttribute($key, $value);
            }
        }
        // stop_measure('fill-model');

        return $this;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->newQuery()->$method(...$parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static())->$method(...$parameters);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     *
     * @throws \Exception
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception($this, json_last_error_msg());
        }

        return $json;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->primaryKey;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     *
     * @return self|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->find($value);
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     *
     * @throws ApiException
     */
    public function save()
    {
        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = $this->isDirty() ? $this->performUpdate() : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert();
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->syncOriginal();
        }

        return $saved;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     *
     * @throws ApiException
     */
    public function update(array $attributes = [])
    {
        $this->fill($attributes)->save();

        return $this;
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function delete()
    {
        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (!$this->exists) {
            return false;
        }

        $this->performDeleteOnModel();

        return true;
    }

    /**
     * Save the model and all of its relationships.
     *
     * @throws
     *
     * @return bool
     */
    public function push()
    {
        if (!$this->save()) {
            return false;
        }

        // To sync all of the relationships to the database, we will simply spin through
        // the relationships and save each model via this "push" method, which allows
        // us to recurse into all of these nested relations for the model instance.
        foreach ($this->relations as $models) {
            $models = $models instanceof self ? [$models] : $models;

            foreach ($models as $model) {
                if (!$model->push()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Perform a model update operation.
     *
     * @return bool
     *
     * @throws ApiException
     */
    protected function performUpdate()
    {
        // Once we have run the update operation, we will fire the "updated" event for
        // this model instance. This will allow developers to hook into these after
        // models are updated, giving them a chance to do any special processing.
        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            $updatedField = $this->getApi()->{'update'.ucfirst($this->getEntity())}($this->{$this->primaryKey}, $dirty);
            $this->fill($updatedField);
            $this->syncChanges();
        }

        return true;
    }

    /**
     * Perform a model insert operation.
     *
     * @return bool
     */
    protected function performInsert()
    {
        $attributes = $this->getAttributes();
        $updatedField = $this->getApi()->{'create'.ucfirst($this->getEntity())}($attributes);
        $this->fill($updatedField);
        $this->exists = true;
        $this->wasRecentlyCreated = true;

        return true;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $this->getApi()->{'delete'.ucfirst($this->getEntity())}($this->{$this->primaryKey});

        $this->exists = false;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder
     */
    public function newQuery()
    {
        return $this->registerGlobalScopes($this->newQueryWithoutScopes());
    }

    /**
     * Register the global scopes for this builder instance.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function registerGlobalScopes($builder)
    {
        foreach ($this->getGlobalScopes() as $identifier => $scope) {
            $builder->withGlobalScope($identifier, $scope);
        }

        return $builder;
    }

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return Builder|static
     */
    public function newQueryWithoutScopes()
    {
        $builder = $this->newBuilder();

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        return $builder->setModel($this);
    }

    /**
     * Create a new query builder for the model.
     *
     * @return Builder
     */
    public function newBuilder()
    {
        return new Builder();
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }
}
