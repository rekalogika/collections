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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Domain\Collections\Common\Internal\KeyTransformerUtil;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait ReadableCollectionTrait
{
    /**
     * @use IteratorAggregateTrait<TKey,T>
     */
    use IteratorAggregateTrait;

    use CountableTrait;

    /**
     * @use FindFetchTrait<TKey,T>
     */
    use FindFetchTrait;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return $this->getSafeCollection()->contains($element);
    }

    final public function isEmpty(): bool
    {
        return $this->getSafeCollection()->isEmpty();
    }

    /**
     * @param mixed $key
     */
    final public function containsKey(mixed $key): bool
    {
        /** @var TKey */
        $key = KeyTransformerUtil::transformInputToKey($this->keyTransformer, $key);

        return $this->getSafeCollection()->containsKey($key);
    }

    /**
     * @param mixed $key
     * @return T|null
     */
    final public function get(mixed $key): mixed
    {
        /** @var TKey */
        $key = KeyTransformerUtil::transformInputToKey($this->keyTransformer, $key);

        return $this->getSafeCollection()->get($key);
    }

    /**
     * @return list<TKey>
     */
    final public function getKeys(): array
    {
        return $this->getSafeCollection()->getKeys();
    }

    /**
     * @return list<T>
     */
    final public function getValues(): array
    {
        return $this->getSafeCollection()->getValues();
    }

    /**
     * @return array<TKey,T>
     */
    final public function toArray(): array
    {
        return $this->getSafeCollection()->toArray();
    }

    /**
     * @return T|false
     */
    final public function first(): mixed
    {
        return $this->getSafeCollection()->first();
    }

    /**
     * @return T|false
     */
    final public function last(): mixed
    {
        return $this->getSafeCollection()->last();
    }

    /**
     * @return TKey|null
     */
    final public function key(): int|string|null
    {
        return $this->getSafeCollection()->key();
    }

    /**
     * @return T|false
     */
    final public function current(): mixed
    {
        return $this->getSafeCollection()->current();
    }

    /**
     * @return T|false
     */
    final public function next(): mixed
    {
        return $this->getSafeCollection()->next();
    }

    /**
     * @return array<TKey,T>
     */
    final public function slice(int $offset, ?int $length = null): array
    {
        return $this->getSafeCollection()->slice($offset, $length);
    }

    /**
     * @param \Closure(TKey, T):bool $p
     */
    final public function exists(\Closure $p): bool
    {
        return $this->getSafeCollection()->exists($p);
    }

    /**
     * @param \Closure(T, TKey):bool $p
     * @return ReadableCollection<TKey,T>
     */
    final public function filter(\Closure $p): ReadableCollection
    {
        return $this->getSafeCollection()->filter($p);
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return ReadableCollection<TKey,U>
     */
    final public function map(\Closure $func): ReadableCollection
    {
        return $this->getSafeCollection()->map($func);
    }

    /**
     * @param \Closure(TKey, T):bool $p
     * @return array{0: ReadableCollection<TKey,T>, 1: ReadableCollection<TKey,T>}
     */
    final public function partition(\Closure $p): array
    {
        return $this->getSafeCollection()->partition($p);
    }

    /**
     * @param \Closure(TKey, T):bool $p
     */
    final public function forAll(\Closure $p): bool
    {
        return $this->getSafeCollection()->forAll($p);
    }

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? TKey|false : false)
     */
    final public function indexOf(mixed $element): bool|int|string
    {
        return $this->getSafeCollection()->indexOf($element);
    }

    /**
     * @param \Closure(TKey, T):bool $p
     * @return T|null
     */
    final public function findFirst(\Closure $p): mixed
    {
        return $this->getSafeCollection()->findFirst($p);
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
        return $this->getSafeCollection()->reduce($func, $initial);
    }
}
