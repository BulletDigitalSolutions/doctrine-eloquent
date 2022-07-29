<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Relationships;

use Doctrine\Common\Collections\Criteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HasOneOrMany extends BaseRelationship
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
     * @var array
     */
    protected $orderBy = [];

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
     * @param $attributes
     * @return mixed|null
     */
    public function firstOrNew($attributes = [])
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

    /**
     * @param $field
     * @param $value
     * @return $this
     */
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
//            case 'like':
//                $this->expressions[] = Criteria::expr()->like($field, $value);
//                break;
//            case 'not like':
//                $this->expressions[] = Criteria::expr()->notLike($field, $value);
//                break;
            case 'in':
                $this->expressions[] = Criteria::expr()->in($field, $value);
                break;
            case 'not in':
                $this->expressions[] = Criteria::expr()->notIn($field, $value);
                break;
            case 'is null':
                $this->expressions[] = Criteria::expr()->isNull($field);
                break;
//            case 'is not null':
//                $this->expressions[] = Criteria::expr()->isNotNull($field);
//                break;
            default:
                throw new \InvalidArgumentException('Invalid operator');
        }

        return $this;
    }

    /**
     * @param $field
     * @param $values
     * @return $this
     */
    public function whereNotIn($field, $values = [])
    {
        $field = Str::camel($field);
        $this->expressions[] = Criteria::expr()->notIn($field, $values);

        return $this;
    }

    /**
     * @return void
     */
    public function orderBy($field, $direction = 'asc')
    {
        $this->orderBy[] = [
            'field' => Str::camel($field),
            'direction' => $direction,
        ];

        return $this;
    }

    /**
     * @return Criteria
     */
    public function getCritera()
    {
        // for first criteria
        $criteria = Criteria::create()->where(Arr::get($this->expressions, 0));

        // for other criteria
        foreach (array_slice($this->expressions, 1) as $expression) {
            $criteria->andWhere($expression);
        }

        if ($this->orderBy) {

//            $orderBy = new OrderBy;
//
//            foreach ($this->orderBy as $order) {
//                $orderBy->add(Arr::get($order, 'field'), Arr::get($order, 'direction'));
//            }
//
//            TODO: Implement ordering
//            $criteria->orderBy($this->orderBy);
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

        $entity = $this->getRelated()?->matching($this->getCritera())->first();

        if ($entity) {
            return $entity;
        }

        return null;
    }

    /**
     * @return null
     */
    public function get()
    {
        if (! count($this->expressions) > 0) {
            return $this->getRelated();
        }

        return $this->getRelated()?->matching($this->getCritera());
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function new($attributes)
    {
        $related = new $this->childEntity;

        $attributes[$this->getLocalKey()] = $this->parent;

        $related->fill($attributes);

        return $related;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function create($attributes)
    {
        $related = $this->new($attributes);

        $related->save();

        return $related;
    }

    /**
     * @return void
     */
    public function delete()
    {
        $entities = $this->get();

        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $entity = $this->firstOrNew($attributes);

        $entity->fill($values);
        $entity->save();

        return $entity;
    }
}
