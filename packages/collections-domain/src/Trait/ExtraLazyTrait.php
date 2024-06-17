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

namespace Rekalogika\Domain\Collections\Trait;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait ExtraLazyTrait
{
    /** @use ReadableExtraLazyTrait<TKey,T> */
    use ReadableExtraLazyTrait;

    //
    // ArrayAccess
    //

    /**
     * @param TKey $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        return $this->collection->offsetExists($offset);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetGet(mixed $offset): mixed
    {
        return $this->collection->offsetGet($offset);
    }

    /**
     * Unsafe if $offset is set. Safe if unset.
     *
     * @param TKey|null $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!isset($offset)) {
            $this->collection->offsetSet(null, $value);

            return;
        }

        $this->getItemsWithSafeguard();
        $this->collection->offsetSet($offset, $value);
    }

    //
    // Collection
    //

    /**
     * Safe
     *
     * @param T $element
     */
    final public function add(mixed $element): void
    {
        $this->collection->add($element);
    }
}
