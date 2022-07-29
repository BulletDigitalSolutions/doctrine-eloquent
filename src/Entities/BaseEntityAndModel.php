<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Entities;

use Illuminate\Database\Eloquent\Model;

class EntityAndModel extends Model
{
    /**
     * @param $key
     * @return mixed|void
     */
    public function __get($key)
    {
        if ($key === 'exists') {
            return $this->exists();
        }
        parent::__get($key);
    }

    /**
     * @return mixed
     */
    public function exists()
    {
        return $this->getRepository()->exists($this);
    }
}
