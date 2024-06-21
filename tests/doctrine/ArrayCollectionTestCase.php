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

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use PHPUnit\Framework\TestCase;

abstract class ArrayCollectionTestCase extends TestCase
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<array-key,mixed>
     */
    abstract protected function buildCollection(array $elements = []): Collection;

    protected function isSelectable(object $obj): bool
    {
        return $obj instanceof Selectable;
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testToArray(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame($elements, $collection->toArray());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testFirst(array $elements): void
    {
        $collection = $this->buildCollection($elements);
        self::assertSame(reset($elements), $collection->first());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testLast(array $elements): void
    {
        $collection = $this->buildCollection($elements);
        self::assertSame(end($elements), $collection->last());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testKey(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(key($elements), $collection->key());

        next($elements);
        $collection->next();

        self::assertSame(key($elements), $collection->key());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testNext(array $elements): void
    {
        $count      = \count($elements);
        $collection = $this->buildCollection($elements);

        for ($i = 0; $i < $count; $i++) {
            $collectionNext = $collection->next();
            $arrayNext      = next($elements);

            if (!$collectionNext || !$arrayNext) {
                break;
            }

            self::assertSame($arrayNext, $collectionNext, 'Returned value of ArrayCollection::next() and next() not match');
            self::assertSame(key($elements), $collection->key(), 'Keys not match');
            self::assertSame(current($elements), $collection->current(), 'Current values not match');
        }

        self::assertFalse($collection->next());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testCurrent(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(current($elements), $collection->current());

        next($elements);
        $collection->next();

        self::assertSame(current($elements), $collection->current());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testGetKeys(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(array_keys($elements), $collection->getKeys());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testGetValues(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(array_values($elements), $collection->getValues());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testCount(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        self::assertSame(\count($elements), $collection->count());
    }

    /**
     * @param array<string|int, string|int> $elements
     *
     * @dataProvider provideDifferentElements
     */
    public function testIterator(array $elements): void
    {
        $collection = $this->buildCollection($elements);

        $iterations = 0;
        foreach ($collection->getIterator() as $key => $item) {
            self::assertSame($elements[$key], $item, 'Item ' . $key . ' not match');
            ++$iterations;
        }

        self::assertEquals(\count($elements), $iterations, 'Number of iterations not match');
    }

    /** @psalm-return array<string, array{mixed[]}> */
    public static function provideDifferentElements(): array
    {
        return [
            'indexed'     => [[1, 2, 3, 4, 5]],
            'associative' => [['A' => 'a', 'B' => 'b', 'C' => 'c']],
            'mixed'       => [['A' => 'a', 1, 'B' => 'b', 2, 3]],
        ];
    }
}
