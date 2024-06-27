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

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\ReadableRecollection;

/**
 * @template-covariant R of ReadableRecollection<array-key,Citizen>
 */
trait ReadableCollectionTestsTrait
{
    /** @use MinimalReadableRecollectionTestsTrait<R> */
    use MinimalReadableRecollectionTestsTrait;

    /** @use IteratorAggregateTestsTrait<R> */
    use IteratorAggregateTestsTrait;

    public function testIsEmpty(): void
    {
        $this->testSafety();
        static::assertFalse($this->getObject()->isEmpty());
    }

    public function testGetKeys(): void
    {
        $this->testSafety();
        $keys = $this->getObject()->getKeys();
        static::assertNotEmpty($keys);
    }

    public function testGetValues(): void
    {
        $this->testSafety();
        $values = $this->getObject()->getValues();
        static::assertIsArray($values);
        static::assertNotEmpty($values);
    }

    public function testToArray(): void
    {
        $this->testSafety();
        $array = $this->getObject()->toArray();
        static::assertIsArray($array);
        static::assertNotEmpty($array);
    }

    public function testFirst(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        static::assertInstanceOf(Citizen::class, $citizen);
    }

    public function testLast(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->last();
        static::assertInstanceOf(Citizen::class, $citizen);
    }

    public function testKey(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        $key = $this->getObject()->key();
        /** @psalm-suppress DocblockTypeContradiction */
        static::assertIsInt($key);
    }

    public function testCurrent(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        $current = $this->getObject()->current();
        static::assertInstanceOf(Citizen::class, $current);
        static::assertSame($citizen, $current);
    }

    public function testNext(): void
    {
        $this->testSafety();
        $first = $this->getObject()->first();
        $next = $this->getObject()->next();
        static::assertInstanceOf(Citizen::class, $next);
        static::assertNotSame($first, $next);
    }

    public function testSlice(): void
    {
        $slice = $this->getObject()->slice(0, 3);
        static::assertIsArray($slice);
        static::assertCount(3, $slice);
    }

    public function testExists(): void
    {
        $this->testSafety();
        $func = fn (int|string $key, mixed $value): bool => (int)$key % 2 === 0;
        $exists = $this->getObject()->exists($func);
        static::assertTrue($exists);
    }

    public function testExistsNegative(): void
    {
        $this->testSafety();
        $func = fn (int|string $key, mixed $value): bool => $key === 9999999;
        $exists = $this->getObject()->exists($func);
        static::assertFalse($exists);
    }

    public function testFilter(): void
    {
        $this->testSafety();
        $func = fn (mixed $value, int|string $key): bool => (int)$key % 2 === 0;
        $filtered = $this->getObject()->filter($func);
        static::assertInstanceOf(ReadableCollection::class, $filtered);

        /** @var int|string $key */
        foreach ($filtered as $key => $citizen) {
            static::assertIsInt($key);
            static::assertTrue($key % 2 === 0);
            static::assertInstanceOf(Citizen::class, $citizen);
        }
    }

    public function testFilterNegative(): void
    {
        $this->testSafety();
        $func = fn (mixed $value, int|string $key): bool => $key === 9999999;
        $filtered = $this->getObject()->filter($func);
        static::assertInstanceOf(ReadableCollection::class, $filtered);
        static::assertCount(0, $filtered);
    }

    public function testMap(): void
    {
        $this->testSafety();
        $func = function (mixed $value): object {
            static::assertIsObject($value);
            return $value;
        };
        $mapped = $this->getObject()->map($func);
        static::assertInstanceOf(ReadableCollection::class, $mapped);
    }

    public function testPartition(): void
    {
        $this->testSafety();
        $func = fn (int|string $key, mixed $value): bool => $key === 3;
        $partitioned = $this->getObject()->partition($func);
        static::assertIsArray($partitioned);
        static::assertCount(2, $partitioned);
    }

    public function testForAll(): void
    {
        $this->testSafety();
        $func = fn (int|string $key, mixed $value): bool => $key > 0;
        $forAll = $this->getObject()->forAll($func);
        static::assertTrue($forAll);
    }

    public function testIndexOf(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        $index = $this->getObject()->indexOf($citizen);
        /** @psalm-suppress DocblockTypeContradiction */
        static::assertIsInt($index);
    }

    public function testFindFirst(): void
    {
        $this->testSafety();
        $func = fn (int|string $key, mixed $value): bool => (int)$key % 2 === 0;
        $first = $this->getObject()->findFirst($func);
        static::assertInstanceOf(Citizen::class, $first);
    }

    public function testReduce(): void
    {
        $this->testSafety();
        $func = fn (?object $carry, object $value): object => $value;
        $reduced = $this->getObject()->reduce($func);
        static::assertInstanceOf(Citizen::class, $reduced);
    }
}
