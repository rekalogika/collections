<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/collections package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Rekalogika\Collections\Tests\IntegrationTests\Trait;

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\Recollection;

/**
 * @template-covariant R of Recollection<array-key,Citizen>
 */
trait CollectionTestsTrait
{
    /** @use MinimalRecollectionTestsTrait<R> */
    use MinimalRecollectionTestsTrait;

    /** @use ArrayAccessTestsTrait<R> */
    use ArrayAccessTestsTrait;

    public function testClear(): void
    {
        $this->testSafety();
        $this->getObject()->clear();
        $array = $this->getObject()->toArray();
        static::assertIsArray($array);
        static::assertCount(0, $array);
    }

    public function testRemove(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        static::assertInstanceOf(Citizen::class, $citizen);

        $key = $citizen->getId();
        static::assertNotNull($key);

        $this->getObject()->remove($key);
        static::assertNull($this->getObject()->get($key));
    }

    public function testRemoveNull(): void
    {
        $this->getObject()->remove(null);

        // @phpstan-ignore staticMethod.alreadyNarrowedType
        static::assertTrue(true);
    }

    public function testRemoveElement(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        static::assertInstanceOf(Citizen::class, $citizen);
        $this->getObject()->removeElement($citizen);
        static::assertFalse($this->getObject()->contains($citizen));
    }

    public function testSet(): void
    {
        $this->testSafety();

        $citizen = new Citizen();
        $citizen->setName('John Doe');
        $citizen->setAge(30);

        $this->getObject()->set(99999, $citizen);
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testSetNull(): void
    {
        $this->testSafety();

        $citizen = new Citizen();
        $citizen->setName('Jane Doe');
        $citizen->setAge(25);

        $this->expectException(InvalidArgumentException::class);
        $this->getObject()->set(null, $citizen);
    }

    // map, filter and partition are already covered in
    // ReadableRecollectionTestsTrait
}
