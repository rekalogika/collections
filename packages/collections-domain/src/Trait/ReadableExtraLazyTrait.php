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
trait ReadableExtraLazyTrait
{
    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return $this->collection->contains($element);
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
     * Safe
     *
     * @return array<TKey,T>
     */

    final public function slice(int $offset, ?int $length = null): array
    {
        return $this->collection->slice($offset, $length);
    }
}
