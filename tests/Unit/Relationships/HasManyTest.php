<?php

declare(strict_types=1);

namespace BulletDigitalSolutions\DoctrineEloquent\Tests\Unit\Relationships;

use BulletDigitalSolutions\DoctrineEloquent\Relationships\HasMany;
use BulletDigitalSolutions\DoctrineEloquent\Traits\Entities\EntityAndModel;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class HasManyTest extends TestCase
{
    public function test_get_returns_related_entities(): void
    {
        $parent = $this->makeParentWithChildren([
            new FakeChild('Alice'),
            new FakeChild('Bob'),
        ]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');

        $this->assertCount(2, $relation->get());
    }

    public function test_first_returns_matching_entity(): void
    {
        $bob = new FakeChild('Bob');
        $parent = $this->makeParentWithChildren([
            new FakeChild('Alice'),
            $bob,
        ]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');
        $relation->where('name', 'Bob');

        $this->assertSame($bob, $relation->first());
    }

    public function test_first_returns_null_when_no_match(): void
    {
        $parent = $this->makeParentWithChildren([new FakeChild('Alice')]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');
        $relation->where('name', 'Missing');

        $this->assertNull($relation->first());
    }

    public function test_create_saves_a_new_child(): void
    {
        $parent = $this->makeParentWithChildren([]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');
        $child = $relation->create(['name' => 'Carol']);

        $this->assertInstanceOf(FakeChild::class, $child);
        $this->assertSame('Carol', $child->getName());
        $this->assertTrue($child->saved);
    }

    public function test_update_or_create_updates_existing_child(): void
    {
        $parent = $this->makeParentWithChildren([new FakeChild('Alice')]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');
        $child = $relation->updateOrCreate(['name' => 'Alice'], ['name' => 'Alicia']);

        $this->assertSame('Alicia', $child->getName());
    }

    public function test_delete_removes_matching_entities(): void
    {
        $alice = new FakeChild('Alice');
        $bob = new FakeChild('Bob');
        $parent = $this->makeParentWithChildren([$alice, $bob]);

        $relation = new HasMany($parent, FakeChild::class, null, null, 'getChildren');
        $relation->where('name', 'Alice')->delete();

        $this->assertTrue($alice->deleted);
        $this->assertFalse($bob->deleted);
    }

    private function makeParentWithChildren(array $children): FakeParent
    {
        return new FakeParent(new ArrayCollection($children));
    }
}

class FakeParent
{
    use EntityAndModel;

    public function __construct(public ArrayCollection $children) {}

    public function getChildren(): ArrayCollection
    {
        return $this->children;
    }

    public function getId(): int
    {
        return 1;
    }
}

class FakeChild
{
    use EntityAndModel;

    public bool $saved = false;

    public bool $deleted = false;

    protected $name;

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setFakeParentId($parent): void
    {
        // Parent assignment is not needed for the tests.
    }

    public function save(array $options = []): bool
    {
        $this->saved = true;

        return true;
    }

    public function delete(): bool
    {
        $this->deleted = true;

        return true;
    }

    public function getId(): ?int
    {
        return null;
    }
}
