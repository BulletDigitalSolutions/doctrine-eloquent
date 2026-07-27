<?php

declare(strict_types=1);

namespace BulletDigitalSolutions\DoctrineEloquent\Tests\Unit\Traits;

use BulletDigitalSolutions\DoctrineEloquent\Traits\ModelableRepository;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ModelableRepositoryTest extends TestCase
{
    public function test_destroy_ids_passes_each_entity_to_destroy(): void
    {
        $entityOne = new stdClass();
        $entityTwo = new stdClass();

        $repository = new class([$entityOne, $entityTwo]) {
            use ModelableRepository;

            public function __construct(public array $entities) {}

            public function getManyBy(array $criteria): array
            {
                return $this->entities;
            }

            public array $destroyed = [];

            public function destroy($entity): bool
            {
                $this->destroyed[] = $entity;

                return true;
            }
        };

        $repository->destroyIds([1, 2]);

        $this->assertSame([$entityOne, $entityTwo], $repository->destroyed);
    }
}
