<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Relationships;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\OrderBy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HasMany extends BaseRelationship
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

        return $this->getRelated()->matching($this->getCritera())->first();
    }


    /**
     * @return null
     */
    public function get()
    {
        if (! count($this->expressions) > 0) {
            return $this->getRelated();
        }

        return $this->getRelated()->matching($this->getCritera());
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function create($attributes)
    {
        $related = new $this->childEntity;

//        dd($this->getLocalKey(), $this->getParentIdentifier());

        $attributes[$this->getLocalKey()] = $this->parent;

//        dd($attributes);

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
