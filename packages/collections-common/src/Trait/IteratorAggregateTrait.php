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

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait IteratorAggregateTrait
{
    /**
     * @return array<TKey,T>
     */
    abstract private function &getItemsWithSafeguard(): array;

    /**
     * @return \Traversable<TKey,T>
     */
    final public function getIterator(): \Traversable
    {
        yield from $this->getItemsWithSafeguard();
    }
}
