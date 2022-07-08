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
     * @param $parent
     * @param $childEntity
     */
    public function __construct($parent, $childEntity)
    {
        $this->parent = $parent;
        $this->childEntity = $childEntity;
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

        $attributes[$this->getParentIdentifierName()] = $this->getParentIdentifier();

        $related->fill($attributes);
        // TODO - Set owner
        $related->save();

        return $related;
    }

    /**
     * @return void
     */
    protected function getRelated()
    {
        // get the filename, remove anything before /
        $array = explode('\\', $this->childEntity);
        $filename = end($array);

        // Pluralise
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
    protected function getParentIdentifierName()
    {
        $name = $this->getParentEntityName();

        return Str::snake(sprintf('%s id', $name));
    }

    /**
     * @return mixed
     */
    protected function getParentIdentifier()
    {
        return $this->parent->getId();
    }
}
