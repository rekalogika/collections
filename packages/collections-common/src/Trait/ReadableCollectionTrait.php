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
use Rekalogika\Domain\Collections\ArrayCollection;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

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

    use RefreshCountTrait;

    /**
     * @use FetchTrait<TKey,T>
     */
    use FetchTrait;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getNewCollection(): Collection;

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return
            $this->getNewCollection()->contains($element)
            || $this->getSafeCollection()->contains($element);
    }

    final public function isEmpty(): bool
    {
        return
            $this->getNewCollection()->isEmpty()
            && $this->getSafeCollection()->isEmpty();
    }

    final public function containsKey(mixed $key): bool
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        return
            $this->getNewCollection()->containsKey($key)
            || $this->getSafeCollection()->containsKey($key);
    }

    /**
     * @return T|null
     */
    final public function get(mixed $key): mixed
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        return
            $this->getNewCollection()->get($key)
            ?? $this->getSafeCollection()->get($key);
    }

    /**
     * @return list<TKey>
     */
    final public function getKeys(): array
    {
        return [
            ...$this->getSafeCollection()->getKeys(),
            ...$this->getNewCollection()->getKeys(),
        ];
    }

    /**
     * @return list<T>
     */
    final public function getValues(): array
    {
        return [
            ...$this->getSafeCollection()->getValues(),
            ...$this->getNewCollection()->getValues(),
        ];
    }

    /**
     * @return array<TKey,T>
     */
    final public function toArray(): array
    {
        return
            $this->getSafeCollection()->toArray()
            + $this->getNewCollection()->toArray();
    }

    /**
     * @return T|false
     */
    final public function first(): mixed
    {
        if ($first = $this->getSafeCollection()->first()) {
            return $first;
        }

        return $this->getNewCollection()->first();
    }

    /**
     * @return T|false
     */
    final public function last(): mixed
    {
        if ($last = $this->getNewCollection()->last()) {
            return $last;
        }

        return $this->getSafeCollection()->last();
    }

    /**
     * @return TKey|null
     * @todo Does not consider items in new collection
     */
    final public function key(): int|string|null
    {
        return $this->getSafeCollection()->key();
    }

    /**
     * @return T|false
     * @todo Does not consider items in new collection
     */
    final public function current(): mixed
    {
        return $this->getSafeCollection()->current();
    }

    /**
     * @return T|false
     * @todo Does not consider items in new collection
     */
    final public function next(): mixed
    {
        return $this->getSafeCollection()->next();
    }

    /**
     * @return array<TKey,T>
     * @todo Does not consider items in new collection
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
        return
            $this->getNewCollection()->exists($p)
            || $this->getSafeCollection()->exists($p);
    }

    /**
     * @param \Closure(T, TKey):bool $p
     * @return ReadableCollection<TKey,T>
     */
    final public function filter(\Closure $p): ReadableCollection
    {
        /** @psalm-suppress DeprecatedClass */
        return new ArrayCollection(
            $this->getSafeCollection()->filter($p)->toArray() +
                $this->getNewCollection()->filter($p)->toArray(),
        );
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return ReadableCollection<TKey,U>
     */
    final public function map(\Closure $func): ReadableCollection
    {
        /** @psalm-suppress DeprecatedClass */
        return new ArrayCollection(
            $this->getSafeCollection()->map($func)->toArray() +
                $this->getNewCollection()->map($func)->toArray(),
        );
    }

    /**
     * @param \Closure(TKey, T):bool $p
     * @return array{0: ReadableCollection<TKey,T>, 1: ReadableCollection<TKey,T>}
     */
    final public function partition(\Closure $p): array
    {
        $safe = $this->getSafeCollection()->partition($p);
        $new = $this->getNewCollection()->partition($p);

        /** @psalm-suppress DeprecatedClass */
        return [
            new ArrayCollection(
                $safe[0]->toArray() + $new[0]->toArray(),
            ),
            new ArrayCollection(
                $safe[1]->toArray() + $new[1]->toArray(),
            ),
        ];
    }

    /**
     * @param \Closure(TKey, T):bool $p
     */
    final public function forAll(\Closure $p): bool
    {
        return
            $this->getSafeCollection()->forAll($p)
            && $this->getNewCollection()->forAll($p);
    }

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? TKey|false : false)
     */
    final public function indexOf(mixed $element): bool|int|string
    {
        if ($this->getNewCollection()->contains($element)) {
            return $this->getNewCollection()->indexOf($element);
        }

        return $this->getSafeCollection()->indexOf($element);
    }

    /**
     * @param \Closure(TKey, T):bool $p
     * @return T|null
     */
    final public function findFirst(\Closure $p): mixed
    {
        return $this->getSafeCollection()->findFirst($p)
            ?? $this->getNewCollection()->findFirst($p);
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
        /** @psalm-suppress DeprecatedClass */
        $collection = new ArrayCollection(
            $this->getSafeCollection()->toArray() +
                $this->getNewCollection()->toArray(),
        );

        return $collection->reduce($func, $initial);
    }
}
