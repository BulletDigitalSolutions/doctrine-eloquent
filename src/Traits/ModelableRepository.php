<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Traits;

trait ModelableRepository
{
    /**
     * @param $entity
     * @param $stripeId
     * @return void
     */
    public function saveChanges($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    /**
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->find($id) !== null;
    }
}
