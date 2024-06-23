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

namespace Rekalogika\Domain\Collections\Common\Trait;

/**
 * @template TKey of array-key
 * @template T
 */
trait ArrayAccessTrait
{
    /**
     * @return array<TKey,T>
     */
    abstract private function &getItemsWithSafeguard(): array;

    /**
     * @param TKey $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        $items = $this->getItemsWithSafeguard();

        return isset($items[$offset]) || \array_key_exists($offset, $items);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetGet(mixed $offset): mixed
    {
        $items = $this->getItemsWithSafeguard();

        return $items[$offset] ?? null;
    }

    /**
     * @param TKey|null $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->getItemsWithSafeguard();

        $this->collection->offsetSet($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetUnset(mixed $offset): void
    {
        $this->getItemsWithSafeguard();

        $this->collection->offsetUnset($offset);
    }
}
