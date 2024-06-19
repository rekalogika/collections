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

use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\IteratorAggregateTrait;
use Rekalogika\Domain\Collections\Common\Trait\ReadableCollectionTrait;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait ReadableExtraLazyTrait
{
    /** @use ReadableCollectionTrait<TKey,T> */
    use ReadableCollectionTrait;

    use CountableTrait;

    /** @use IteratorAggregateTrait<TKey,T> */
    use IteratorAggregateTrait;

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        if ($this->isExtraLazy()) {
            return $this->collection->contains($element);
        }

        $items = $this->getItemsWithSafeguard();

        return \in_array($element, $items, true);
    }

    /**
     * Safe
     *
     * @param TKey $key
     */
    final public function containsKey(string|int $key): bool
    {
        if ($this->isExtraLazy() && $this->hasIndexBy()) {
            return $this->collection->containsKey($key);
        }

        $items = $this->getItemsWithSafeguard();

        return isset($items[$key]) || \array_key_exists($key, $items);
    }

    /**
     * Safe
     *
     * @param TKey $key
     * @return T|null
     */
    final public function get(string|int $key): mixed
    {
        if ($this->isExtraLazy() && $this->hasIndexBy()) {
            return $this->collection->get($key);
        }

        $items = $this->getItemsWithSafeguard();

        return $items[$key] ?? null;
    }

    /**
     * Safe
     *
     * @return array<TKey,T>
     */

    final public function slice(int $offset, ?int $length = null): array
    {
        if ($this->isExtraLazy()) {
            return $this->collection->slice($offset, $length);
        }

        $items = $this->getItemsWithSafeguard();

        return \array_slice($items, $offset, $length, true);
    }
}
