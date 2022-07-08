<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Contracts;

interface ModelableEntityContract
{
    /**
     * @return mixed
     */
    public function getRepository();
}
