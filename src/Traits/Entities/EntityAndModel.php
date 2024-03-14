<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Traits\Entities;

use Illuminate\Support\Str;

trait EntityAndModel
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

        if ($key === 'stripe_id') {
            dd($value);
        }

        if (method_exists($this, $function)) {
            return $this->{$function}($value);
        }

        if (Str::endsWith($key, '_id')) {
            $function = Str::camel(sprintf('set %s', Str::replaceLast('_id', '', $key)));
            if (method_exists($this, $function)) {
                return $this->{$function}();
            }
        }
        //        TODO: Throw error if doesnt exist
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function fill($attributes)
    {
//        TODO: Protected Properties ?
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->exists = $this->exists();
    }

    /**
     * @return mixed
     */
    public function exists()
    {
        return $this->getRepository()->exists($this);
    }

    /**
     * @param $exists
     * @return $this
     */
    public function setExists($exists)
    {
        $this->exists = $exists;

        return $this;
    }

    /**
     * @return mixed
     */
    public function save(array $options = [])
    {
        // TODO: Accept options;
        return $this->getRepository()->saveChanges($this);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getId() ?: '';
    }
}
