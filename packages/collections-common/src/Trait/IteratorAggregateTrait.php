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

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait IteratorAggregateTrait
{
    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getNewCollection(): Collection;

    /**
     * @return \Traversable<TKey,T>
     */
    final public function getIterator(): \Traversable
    {
        foreach ($this->getSafeCollection() as $key => $value) {
            yield $key => $value;
        }

        foreach ($this->getNewCollection() as $key => $value) {
            yield $key => $value;
        }
    }
}
