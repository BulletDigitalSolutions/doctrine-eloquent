<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Traits\Entities;

use BulletDigitalSolutions\DoctrineEloquent\Relationships\HasMany;
use Illuminate\Support\Str;

trait Modelable
{
    /**
     * @param $name
     * @param $arguments
     * @return |null
     */
    public function __get($key)
    {
        $function = Str::camel(sprintf('get %s', $key));

        if (method_exists($this, $function)) {
            return $this->{$function}();
        }

        if (Str::endsWith($key, '_id')) {
            $function = Str::camel(sprintf('get %s', Str::replaceLast('_id', '', $key)));
            if (method_exists($this, $function)) {
                return $this->{$function}();
            }
        }
//        TODO: Throw error if doesnt exist
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function __set($key, $value)
    {
        $function = Str::camel(sprintf('set %s', $key));

        if (method_exists($this, $function)) {
            return $this->{$function}($value);
        }

        if (Str::endsWith($key, '_id')) {
            $function = Str::camel(sprintf('set %s', Str::replaceLast('_id', '', $key)));
            if (method_exists($this, $function)) {
                return $this->{$function}($value);
            }
        }
        //        TODO: Throw error if doesnt exist
    }

    /**
     * @return mixed
     */
    public function save(array $options = [])
    {
        return $this->getRepository()->saveChanges($this);
    }

    /**
     * @return mixed
     */
    public function saveOrFail(array $options = [])
    {
        // TODO
        return $this->getRepository()->saveChanges($this);
    }

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(array $options = [])
    {
        // TODO
        return $this->getRepository()->saveChanges($this);
    }

    /**
     * @param  array  $attributes
     * @return $this
     */
    public static function create(array $attributes)
    {
        $entity = new self();

        $entity->fill($attributes);
        $entity->save();

        return $entity;
    }

    /**
     * @param  array  $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
//        TODO: Protected Properties ?
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function forceFill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        // TODO: handle exists
//        if (! $this->exists) {
//            return false;
//        }

        return $this->fill($attributes)->save($options);
    }

    /**
     * Update the model in the database within a transaction.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     *
     * @throws \Throwable
     */
    public function updateOrFail(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->saveOrFail($options);
    }

    /**
     * Update the model in the database without raising any events.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function updateQuietly(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->saveQuietly($options);
    }

    /**
     * @return mixed
     */
    public static function destroy($ids)
    {
        $repository = new self();
        $repository = $repository->getRepository();

        return $repository->destroyIds($ids);
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        return $this->getRepository()->destroy($this);
    }

    /**
     * @return bool|null
     *
     * @throws \Throwable
     */
    public function deleteOrFail()
    {
//        TODO
        return $this->getRepository()->destroy($this);
    }

    /**
     * @return bool|null
     */
    public function forceDelete()
    {
//        TODO
        return $this->getRepository()->destroy($this);
    }

    /**
     * @param $relatedEntity
     * @param $foreignKey
     * @param $localKey
     * @return HasMany
     */
    public function hasMany($relatedEntity, $foreignKey = null, $localKey = null)
    {
        return new HasMany($this, $relatedEntity);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function find($id)
    {
        return self::getRepository()->find($id);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        // TODO: This should be tidier
        return $this->getId() ?: '';
    }

    /**
     * @return mixed
     */
    public function getExists()
    {
        return $this->getRepository()->exists($this);
    }

//    TODO
    public function getForeignKey()
    {
        return 'id';
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $array = [];

        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
