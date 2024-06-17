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
 */
trait IteratorAggregateTrait
{
    /**
     * Unsafe
     *
     * @return \Traversable<TKey,T>
     */
    final public function getIterator(): \Traversable
    {
        yield from $this->getItemsWithSafeguard();
    }
}
