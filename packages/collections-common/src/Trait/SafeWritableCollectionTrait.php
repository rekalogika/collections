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
 * @template T
 */
trait SafeWritableCollectionTrait
{
    /**
     * @param T $element
     */
    final public function add(mixed $element): void
    {
        $this->collection->add($element);
    }
}
