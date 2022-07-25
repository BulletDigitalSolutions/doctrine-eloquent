<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Traits\Entities;

use BulletDigitalSolutions\DoctrineEloquent\Relationships\HasMany;
use Illuminate\Support\Str;

trait EntityAndModel
{

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
    public function __toString()
    {
        return $this->getId();
    }
}
