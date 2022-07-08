<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Relationships;

use Doctrine\Common\Collections\Criteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HasMany
{
    /**
     * @var
     */
    protected $parent;

    /**
     * @var
     */
    protected $childEntity;

    /**
     * @var array
     */
    protected $expressions = [];

    /**
     * @var mixed|null
     */
    private $foreignKey;

    /**
     * @var mixed|null
     */
    private $localKey;

    /**
     * @var mixed|null
     */
    private $getter;

    /**
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
     * @param $field
     * @param $value
     * @return $this
     */
    public function where($field, $value)
    {
        $field = Str::camel($field);
        $this->expressions[] = Criteria::expr()->eq($field, $value);

        return $this;
    }

    /**
     * @return void
     */
    public function orderBy()
    {
        // TODO: implement
    }

    public function getCritera()
    {
        // for first criteria
        $criteria = Criteria::create()->where(Arr::get($this->expressions, 0));

        // for other criteria
        foreach (array_slice($this->expressions, 1) as $expression) {
            $criteria->andWhere($expression);
        }

        return $criteria;
    }

    /**
     * @return null
     */
    public function first()
    {
        if (! count($this->expressions) > 0) {
            return $this->getRelated()?->first();
        }

        return $this->getRelated()->matching($this->getCritera())->first();
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function create($attributes)
    {
        $related = new $this->childEntity;

        $attributes[$this->getLocalKey()] = $this->getParentIdentifier();

        $related->fill($attributes);
        $related->save();

        return $related;
    }

    /**
     * @return void
     */
    protected function getRelated()
    {
        if ($this->getter) {
            return $this->parent->{$this->getter}();
        }

        $array = explode('\\', $this->childEntity);
        $filename = end($array);
        $filename = Str::plural($filename);

        $function = Str::camel(sprintf('get %s', $filename));

        if (method_exists($this->parent, $function)) {
            return $this->parent->{$function}();
        }
        // TODO: throw error if doesnt exist
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
