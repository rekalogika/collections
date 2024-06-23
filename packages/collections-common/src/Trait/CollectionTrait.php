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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @template TKey of array-key
 * @template T
 */
trait CollectionTrait
{
    /**
     * @param T $element
     */
    final public function add(mixed $element): void
    {
        $this->getItemsWithSafeguard();
        $this->collection->add($element);
    }

    final public function clear(): void
    {
        $this->getItemsWithSafeguard();
        $this->collection->clear();
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    final public function remove(string|int $key): mixed
    {
        $this->getItemsWithSafeguard();
        return $this->collection->remove($key);
    }

    /**
     * @param T $element
     */
    final public function removeElement(mixed $element): bool
    {
        $this->getItemsWithSafeguard();
        return $this->collection->removeElement($element);
    }

    /**
     * @param TKey $key
     * @param T $value
     */
    final public function set(string|int $key, mixed $value): void
    {
        $this->getItemsWithSafeguard();
        $this->collection->set($key, $value);
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return Collection<TKey,U>
     */
    final public function map(\Closure $func): Collection
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();
        $result = array_map($func, $items);

        return new ArrayCollection($result);
    }

    /**
     * @param \Closure(T, TKey):bool $p
     * @return Collection<TKey,T>
     */
    final public function filter(\Closure $p): Collection
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();
        $result = array_filter($items, $p, \ARRAY_FILTER_USE_BOTH);

        return new ArrayCollection($result);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     * @return array{0: Collection<TKey,T>, 1: Collection<TKey,T>}
     */
    final public function partition(\Closure $p): array
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        $matches = $noMatches = [];

        foreach ($items as $key => $item) {
            if ($p($key, $item)) {
                $matches[$key] = $item;
            } else {
                $noMatches[$key] = $item;
            }
        }

        return [new ArrayCollection($matches), new ArrayCollection($noMatches)];
    }
}
