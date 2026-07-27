<?php

declare(strict_types=1);

namespace BulletDigitalSolutions\DoctrineEloquent\Traits\Relationships;

use Closure;
use Doctrine\Common\Collections\Criteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait DoctrineRelationTrait
{
    /**
     * @var mixed
     */
    protected $parent;

    /**
     * @var mixed
     */
    protected $childEntity;

    /**
     * @var array
     */
    protected $expressions = [];

    /**
     * @var array
     */
    protected $orderBy = [];

    /**
     * @var mixed|null
     */
    protected $foreignKey;

    /**
     * @var mixed|null
     */
    protected $localKey;

    /**
     * @var mixed|null
     */
    protected $getter;

    public function __construct($parent, $childEntity, $foreignKey = null, $localKey = null, $getter = null)
    {
        $this->parent = $parent;
        $this->childEntity = $childEntity;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->getter = $getter;
    }

    public function firstOrNew(array $attributes = [], Closure|array $values = [])
    {
        foreach ($attributes as $key => $value) {
            $this->where($key, '=', $value);
        }

        $entity = $this->first();

        if (! $entity) {
            $entity = $this->new($attributes);
        }

        return $entity;
    }

    public function where($field, $operator, $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }

        $field = Str::camel($field);

        switch ($operator) {
            case '=':
                $this->expressions[] = Criteria::expr()->eq($field, $value);
                break;
            case '!=':
                $this->expressions[] = Criteria::expr()->neq($field, $value);
                break;
            case '>':
                $this->expressions[] = Criteria::expr()->gt($field, $value);
                break;
            case '>=':
                $this->expressions[] = Criteria::expr()->gte($field, $value);
                break;
            case '<':
                $this->expressions[] = Criteria::expr()->lt($field, $value);
                break;
            case '<=':
                $this->expressions[] = Criteria::expr()->lte($field, $value);
                break;
            case 'in':
                $this->expressions[] = Criteria::expr()->in($field, $value);
                break;
            case 'not in':
                $this->expressions[] = Criteria::expr()->notIn($field, $value);
                break;
            case 'is null':
                $this->expressions[] = Criteria::expr()->isNull($field);
                break;
            default:
                throw new \InvalidArgumentException('Invalid operator');
        }

        return $this;
    }

    public function whereNotIn($field, $values = [])
    {
        if (is_object($values) && method_exists($values, 'toArray')) {
            $values = $values->toArray();
        }

        $field = Str::camel($field);
        $this->expressions[] = Criteria::expr()->notIn($field, $values);

        return $this;
    }

    public function orderBy($field, $direction = 'asc')
    {
        $this->orderBy[] = [
            'field' => Str::camel($field),
            'direction' => $direction,
        ];

        return $this;
    }

    public function getCriteria()
    {
        $criteria = Criteria::create()->where(Arr::get($this->expressions, 0));

        foreach (array_slice($this->expressions, 1) as $expression) {
            $criteria->andWhere($expression);
        }

        return $criteria;
    }

    public function first()
    {
        if (! count($this->expressions) > 0) {
            return $this->getRelated()?->first();
        }

        $entity = $this->getRelated()?->matching($this->getCriteria())->first();

        if ($entity) {
            return $entity;
        }

        return null;
    }

    public function get($columns = ['*'])
    {
        if (! count($this->expressions) > 0) {
            return $this->getRelated();
        }

        return $this->getRelated()?->matching($this->getCriteria());
    }

    public function new($attributes)
    {
        $related = new $this->childEntity;

        $attributes[$this->getLocalKey()] = $this->parent;

        $related->fill($attributes);

        return $related;
    }

    public function create(array $attributes = [])
    {
        $related = $this->new($attributes);

        $related->save();

        return $related;
    }

    public function delete()
    {
        $entities = $this->get();

        if (! $entities) {
            return;
        }

        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

    public function updateOrCreate(array $attributes, Closure|array $values = [])
    {
        $entity = $this->firstOrNew($attributes);

        $entity->fill($values);
        $entity->save();

        return $entity;
    }

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

        return null;
    }

    protected function getParentEntityName()
    {
        $array = explode('\\', get_class($this->parent));

        return end($array);
    }

    protected function getLocalKey()
    {
        if ($this->localKey) {
            return $this->localKey;
        }

        $name = $this->getParentEntityName();

        return Str::snake(sprintf('%s id', $name));
    }

    protected function getParentIdentifier()
    {
        return $this->parent->getId();
    }
}
