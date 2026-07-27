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
        return [
            'query' => $this->query,
            'child' => $this->child,
            'foreignKey' => $this->foreignKey,
            'ownerKey' => $this->ownerKey,
            'relation' => $this->relation,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->query = $data['query'];
        $this->child = $data['child'];
        $this->foreignKey = $data['foreignKey'];
        $this->ownerKey = $data['ownerKey'];
        $this->relation = $data['relation'];
    }
}
