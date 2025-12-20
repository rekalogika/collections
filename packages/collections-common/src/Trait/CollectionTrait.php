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
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

/**
 * @template TKey of array-key
 * @template T
 */
trait CollectionTrait
{
    /**
     * @use ReadableCollectionTrait<TKey,T>
     */
    use ReadableCollectionTrait;

    /**
     * @use ArrayAccessTrait<TKey,T>
     */
    use ArrayAccessTrait;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getRealCollection(): Collection;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    abstract private function ensureSafety(): void;

    /**
     * @param T $element
     */
    final public function add(mixed $element): void
    {
        $this->getRealCollection()->add($element);
    }

    final public function clear(): void
    {
        $this->getSafeCollection()->clear();
        $this->getRealCollection()->clear();
    }

    /**
     * @return T|null
     */
    final public function remove(mixed $key): mixed
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        if ($key === null) {
            return null;
        }

        $this->getSafeCollection()->remove($key);
        return $this->getRealCollection()->remove($key);
    }

    /**
     * @param T $element
     */
    final public function removeElement(mixed $element): bool
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $this->getSafeCollection()->removeElement($element);
        return $this->getRealCollection()->removeElement($element);
    }

    /**
     * @param T $value
     */
    final public function set(mixed $key, mixed $value): void
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        if ($key === null) {
            throw new InvalidArgumentException('Null key provided.');
        }

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $this->getSafeCollection()->set($key, $value);
        $this->getRealCollection()->set($key, $value);
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return Collection<TKey,U>
     */
    final public function map(\Closure $func): Collection
    {
        $result = $this->getSafeCollection()->map($func);

        if (!$result instanceof Collection) {
            throw new \RuntimeException('Unexpected return type from map');
        }

        return $result;
    }

    /**
     * @param \Closure(T, TKey):bool $p
     * @return Collection<TKey,T>
     */
    final public function filter(\Closure $p): Collection
    {
        $result = $this->getSafeCollection()->filter($p);

        if (!$result instanceof Collection) {
            throw new \RuntimeException('Unexpected return type from filter');
        }

        return $result;
    }

    /**
     * @param \Closure(TKey,T):bool $p
     * @return array{0: Collection<TKey,T>, 1: Collection<TKey,T>}
     */
    final public function partition(\Closure $p): array
    {
        $result = $this->getSafeCollection()->partition($p);

        if (!\is_array($result) || \count($result) !== 2) {
            throw new \RuntimeException('Unexpected return type from partition');
        }

        return $result;
    }
}
