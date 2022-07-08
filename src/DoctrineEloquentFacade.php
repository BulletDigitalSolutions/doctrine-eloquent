<?php

namespace BulletDigitalSolutions\DoctrineEloquent;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BulletDigitalSolutions\DoctrineEloquent\Skeleton\SkeletonClass
 */
class DoctrineEloquentFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'doctrine-eloquent';
    }
}
