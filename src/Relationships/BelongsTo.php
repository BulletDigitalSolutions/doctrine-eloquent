<?php

namespace BulletDigitalSolutions\DoctrineEloquent\Relationships;

class BelongsTo extends BaseRelationship
{
    /**
     * @var
     */
    private $query;

    private $child;

    private $foreignKey;

    private $ownerKey;

    private $relation;

    public function __construct($query, $child = null, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $this->query = $query;
        $this->child = $child;
        $this->foreignKey = $foreignKey;
        $this->ownerKey = $ownerKey;
        $this->relation = $relation;
    }

    public function __serialize(): array
    {
        dd('__serialize');
        // TODO: Implement __serialize() method.
    }
}
