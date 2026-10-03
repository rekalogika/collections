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

namespace Rekalogika\Domain\Collections;

use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;

/**
 * @template TKey of array-key
 * @template T
 * @extends DoctrineArrayCollection<TKey,T>
 * @api
 * @deprecated Use doctrine/collections >=2.4 instead, see https://github.com/doctrine/collections/pull/472
 */
class ArrayCollection extends DoctrineArrayCollection
{
    /**
     * @param array<TKey,T> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }
}
