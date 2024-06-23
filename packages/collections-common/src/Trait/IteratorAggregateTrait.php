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

use Doctrine\Common\Collections\ReadableCollection;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait IteratorAggregateTrait
{
    /**
     * @return ReadableCollection<TKey,T>
     */
    abstract private function getSafeCollection(): ReadableCollection;

    /**
     * @return \Traversable<TKey,T>
     */
    final public function getIterator(): \Traversable
    {
        return $this->getSafeCollection()->getIterator();
    }
}
