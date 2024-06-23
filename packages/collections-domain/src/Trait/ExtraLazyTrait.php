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

use Rekalogika\Domain\Collections\Common\Trait\ArrayAccessTrait;
use Rekalogika\Domain\Collections\Common\Trait\CollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\IteratorAggregateTrait;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait ExtraLazyTrait
{
    /**
     * @use CollectionTrait<TKey,T>
     * @use ReadableExtraLazyTrait<TKey,T>
     */
    use CollectionTrait, ReadableExtraLazyTrait {
        CollectionTrait::filter insteadof ReadableExtraLazyTrait;
        CollectionTrait::map insteadof ReadableExtraLazyTrait;
        CollectionTrait::partition insteadof ReadableExtraLazyTrait;
    }

    use CountableTrait;

    /** @use IteratorAggregateTrait<TKey,T> */
    use IteratorAggregateTrait;

    /** @use ArrayAccessTrait<TKey,T> */
    use ArrayAccessTrait;

    //
    // ArrayAccess
    //

    /**
     * @param TKey $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        if ($this->isExtraLazy() && $this->hasIndexBy()) {
            return $this->collection->offsetExists($offset);
        }

        $items = $this->getItemsWithSafeguard();

        return isset($items[$offset]) || \array_key_exists($offset, $items);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetGet(mixed $offset): mixed
    {
        if ($this->isExtraLazy() && $this->hasIndexBy()) {
            return $this->collection->get($offset);
        }

        $items = $this->getItemsWithSafeguard();

        return $items[$offset] ?? null;
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
