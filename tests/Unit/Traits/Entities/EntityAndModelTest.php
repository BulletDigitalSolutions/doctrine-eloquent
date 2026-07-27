<?php

declare(strict_types=1);

namespace BulletDigitalSolutions\DoctrineEloquent\Tests\Unit\Traits\Entities;

use BulletDigitalSolutions\DoctrineEloquent\Traits\Entities\EntityAndModel;
use PHPUnit\Framework\TestCase;

final class EntityAndModelTest extends TestCase
{
    public function test_magic_set_calls_named_setter_with_value(): void
    {
        $entity = $this->makeEntity();

        $entity->name = 'foo';

        $this->assertSame('foo', $entity->getName());
    }

    public function test_magic_set_calls_id_setter_with_value(): void
    {
        $entity = $this->makeEntity();

        $entity->user_id = 123;

        $this->assertSame(123, $entity->getUser());
    }

    public function test_magic_set_does_not_call_dd_on_stripe_id(): void
    {
        $entity = $this->makeEntity();

        // A missing setter should not trigger a dd() or fatal.
        $entity->stripe_id = 'cus_123';

        $this->assertTrue(true);
    }

    public function test_magic_get_calls_named_getter(): void
    {
        $entity = $this->makeEntity();
        $entity->setName('bar');

        $this->assertSame('bar', $entity->name);
    }

    public function test_magic_get_calls_id_getter(): void
    {
        $entity = $this->makeEntity();
        $entity->setUser(456);

        $this->assertSame(456, $entity->user_id);
    }

    private function makeEntity()
    {
        return new class {
            use EntityAndModel;

            protected $name;
            protected $user;

            public function setName($value): void
            {
                $this->name = $value;
            }

            public function getName()
            {
                return $this->name;
            }

            public function setUser($value): void
            {
                $this->user = $value;
            }

            public function getUser()
            {
                return $this->user;
            }
        };
    }
}
