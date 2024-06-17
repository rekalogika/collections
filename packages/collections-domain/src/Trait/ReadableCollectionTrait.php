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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\ReadableCollection;

/**
 * @template TKey of array-key
 * @template T
 */
trait ReadableCollectionTrait
{
    /** @use IteratorAggregateTrait<TKey,T> */
    use IteratorAggregateTrait;

    use CountableTrait;

    /**
     * Safe
     *
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return $this->collection->contains($element);
    }

    /**
     * Unsafe
     */
    final public function isEmpty(): bool
    {
        return empty($this->getItemsWithSafeguard());
    }

    /**
     * Safe
     *
     * @param TKey $key
     */
    final public function containsKey(string|int $key): bool
    {
        return $this->collection->containsKey($key);
    }

    /**
     * Safe
     *
     * @param TKey $key
     * @return T|null
     */
    final public function get(string|int $key): mixed
    {
        return $this->collection->get($key);
    }

    /**
     * Unsafe
     *
     * @return list<TKey>
     */
    final public function getKeys(): array
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        return array_keys($items);
    }

    /**
     * Unsafe
     *
     * @return list<T>
     */
    final public function getValues(): array
    {
        return array_values($this->getItemsWithSafeguard());
    }

    /**
     * Unsafe
     *
     * @return array<TKey,T>
     */
    final public function toArray(): array
    {
        return $this->getItemsWithSafeguard();
    }

    /**
     * Unsafe
     *
     * @return T|false
     */
    final public function first(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return reset($array);
    }

    /**
     * Unsafe
     *
     * @return T|false
     */
    final public function last(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return end($array);
    }

    /**
     * Unsafe
     *
     * @return TKey|null
     */
    final public function key(): int|string|null
    {
        $array = &$this->getItemsWithSafeguard();

        return key($array);
    }

    /**
     * Unsafe
     *
     * @return T|false
     */
    final public function current(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return current($array);
    }

    /**
     * Unsafe
     *
     * @return T|false
     */
    final public function next(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return next($array);
    }

    /**
     * Safe
     *
     * @return array<TKey,T>
     */

    final public function slice(int $offset, ?int $length = null): array
    {
        return $this->collection->slice($offset, $length);
    }

    /**
     * Unsafe
     *
     * @param \Closure(TKey, T):bool $p
     */
    final public function exists(\Closure $p): bool
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        foreach ($items as $key => $item) {
            if ($p($key, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Unsafe
     *
     * @param \Closure(T, TKey):bool $p
     * @return ReadableCollection<TKey,T>
     */
    final public function filter(\Closure $p): ReadableCollection
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        $result = array_filter($items, $p, \ARRAY_FILTER_USE_BOTH);

        return new ArrayCollection($result);
    }

    /**
     * Unsafe
     *
     * @template U
     * @param \Closure(T):U $func
     * @return ReadableCollection<TKey,U>
     */
    final public function map(\Closure $func): ReadableCollection
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        $result = array_map($func, $items);

        return new ArrayCollection($result);
    }

    /**
     * Unsafe
     *
     * @param \Closure(TKey, T):bool $p
     * @return array{0: ReadableCollection<TKey,T>, 1: ReadableCollection<TKey,T>}
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

    /**
     * Unsafe
     *
     * @param \Closure(TKey, T):bool $p
     */
    final public function forAll(\Closure $p): bool
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        foreach ($items as $key => $item) {
            if (!$p($key, $item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Unsafe
     *
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? TKey|false : false)
     */
    final public function indexOf(mixed $element): bool|int|string
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        return array_search($element, $items, true);
    }

    /**
     * Unsafe
     *
     * @param \Closure(TKey, T):bool $p
     * @return T|null
     */
    final public function findFirst(\Closure $p): mixed
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        foreach ($items as $key => $item) {
            if ($p($key, $item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Unsafe
     *
     * @template TReturn
     * @template TInitial
     * @param \Closure(TReturn|TInitial, T):TReturn $func
     * @param TInitial $initial
     * @return TReturn|TInitial
     */
    final public function reduce(\Closure $func, mixed $initial = null): mixed
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        return array_reduce($items, $func, $initial);
    }
}
