<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Traits;

trait ModelableRepository
{
    /**
     * @param $entity
     * @param $stripeId
     * @return void
     */
    // Rename to persist and flush
    public function saveChanges($entity)
    {
        // Change to app em
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }
}
