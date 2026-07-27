<?php

declare(strict_types=1);

namespace BulletDigitalSolutions\DoctrineEloquent\Tests\Unit\Relationships;

use BulletDigitalSolutions\DoctrineEloquent\Relationships\BelongsTo;
use PHPUnit\Framework\TestCase;
use stdClass;

final class BelongsToTest extends TestCase
{
    public function test_it_can_be_serialized_and_unserialized(): void
    {
        $child = new stdClass();
        $relation = new BelongsTo('query', $child, 'foreign_key', 'owner_key', 'relation');

        $serialized = serialize($relation);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(BelongsTo::class, $unserialized);
    }
}
