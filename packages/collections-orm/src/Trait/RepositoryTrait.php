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

namespace Rekalogika\Collections\ORM\Trait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;

/**
 * @template TKey of array-key
 * @template T of object
 *
 * @internal
 */
trait RepositoryTrait
{
    final public function isEmpty(): bool
    {
        return empty($this->getItemsWithSafeguard());
    }

    /**
     * @return list<TKey>
     */
    final public function getKeys(): array
    {
        /** @var array<TKey,T> */
        $items = $this->getItemsWithSafeguard();

        return array_keys($items);
    }

    /**
     * @return list<T>
     */
    final public function getValues(): array
    {
        return array_values($this->getItemsWithSafeguard());
    }

    /**
     * @return array<TKey,T>
     */
    final public function toArray(): array
    {
        return $this->getItemsWithSafeguard();
    }

    /**
     * @return T|false
     */
    final public function first(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return reset($array);
    }

    /**
     * @return T|false
     */
    final public function last(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return end($array);
    }

    /**
     * @return TKey|null
     */
    final public function key(): int|string|null
    {
        $array = &$this->getItemsWithSafeguard();

        return key($array);
    }

    /**
     * @return T|false
     */
    final public function current(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return current($array);
    }

    /**
     * @return T|false
     */
    final public function next(): mixed
    {
        $array = &$this->getItemsWithSafeguard();

        return next($array);
    }

    /**
     * @return array<TKey,T>
     */
    final public function slice(int $offset, ?int $length = null): array
    {
        $items = $this->getItemsWithSafeguard();

        return \array_slice($items, $offset, $length, true);
    }

    /**
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
     * @param \Closure(TKey, T):bool $p
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

    /**
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

    public function clear()
    {
    }

    public function set(string|int $key, mixed $value)
    {
    }

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
     * @return T|null
     */
    final public function offsetGet(mixed $offset): mixed
    {
        $items = $this->getItemsWithSafeguard();

        /** @var T|null */
        return $items[$offset] ?? null;
    }

    /**
     * @param TKey|null $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->add($value);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
