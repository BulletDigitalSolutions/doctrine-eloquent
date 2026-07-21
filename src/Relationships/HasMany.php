<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Relationships;

use Illuminate\Support\Str;

class HasMany extends HasOneOrMany
{
    /**
     * The properties this class needs (parent, childEntity, expressions, orderBy,
     * foreignKey, localKey, getter) are all declared on HasOneOrMany and inherited.
     *
     * @param $parent
     * @param $childEntity
     */
    public function __construct($parent, $childEntity, $foreignKey = null, $localKey = null, $getter = null)
    {
        $this->parent = $parent;
        $this->childEntity = $childEntity;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->getter = $getter;
    }

    /**
     * @return void
     */
    public function getRelated()
    {
        if ($this->getter) {
            return $this->parent->{$this->getter}();
        }

        $array = explode('\\', $this->childEntity);
        $filename = end($array);
        $filename = Str::plural($filename);

        $function = Str::camel(sprintf('get %s', $filename));

        if (method_exists($this->parent, $function)) {
            $entities = $this->parent->{$function}();

            return $entities->map(function ($entity) {
                $entity->exists = true;

                return $entity;
            });
        }
    }

    /**
     * @return false|mixed|string
     */
    protected function getParentEntityName()
    {
        $array = explode('\\', get_class($this->parent));

        return end($array);
    }

    /**
     * @return string
     */
    protected function getLocalKey()
    {
        if ($this->localKey) {
            return $this->localKey;
        }

        $name = $this->getParentEntityName();

        return Str::snake(sprintf('%s id', $name));
    }

    /**
     * @return mixed
     */
    protected function getParentIdentifier()
    {
        // TODO
        return $this->parent->getId();
    }
}
