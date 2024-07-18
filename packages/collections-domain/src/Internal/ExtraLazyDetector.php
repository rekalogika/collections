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

namespace Rekalogika\Domain\Collections\Internal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\ReadableCollection;

/**
 * @internal
 */
final class ExtraLazyDetector
{
    private function __construct()
    {
    }

    /**
     * @template TKey of array-key
     * @template T
     * @param ReadableCollection<TKey,T> $collection
     */
    public static function isSafe(ReadableCollection $collection): bool
    {
        if ($collection instanceof ArrayCollection) {
            return true;
        }

        return self::isExtraLazy($collection);
    }

    /**
     * @template TKey of array-key
     * @template T
     * @param ReadableCollection<TKey,T> $collection
     */
    public static function isSafeWithIndex(ReadableCollection $collection): bool
    {
        return self::isSafe($collection) && self::hasIndexBy($collection);
    }

    /**
     * @template TKey of array-key
     * @template T
     */
    public static function isExtraLazy(): bool
    {
        return true;
        // disabled for now
    }

    /**
     * @template TKey of array-key
     * @template T
     */
    public static function hasIndexBy(): bool
    {
        return true;
        // disabled for now
    }

}
