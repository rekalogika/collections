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

namespace Rekalogika\Domain\Collections\Common;

use Doctrine\Common\Collections\Order;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\DefaultKeyTransformer;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;

final class Configuration
{
    private function __construct()
    {
    }

    /**
     * If the collection has more than this number of items, a deprecation
     * notice will be emitted.
     *
     * @var int<1,max>
     */
    public static int $defaultSoftLimit = 500;

    /**
     * If the collection has more than this number of items, an exception will
     * be thrown.
     *
     * @var int<1,max>
     */
    public static int $defaultHardLimit = 2000;

    /**
     * @var int<1,max>
     */
    public static int $defaultCountSoftLimit = 5000;

    /**
     * @var int<1,max>
     */
    public static int $defaultCountHardLimit = 50000;

    public static float $defaultCountDurationThreshold = 2;

    /**
     * The default order by clause for the collection.
     *
     * @var non-empty-array<string,Order>
     */
    public static array $defaultOrderBy = ['id' => Order::Descending];

    /**
     * @var class-string<KeyTransformer>
     */
    public static string $defaultKeyTransformer = DefaultKeyTransformer::class;

    /**
     * @var null|\Closure(): CountStrategy
     */
    public static ?\Closure $defaultCountStrategyForFullClasses = null;

    /**
     * @var null|\Closure(): CountStrategy
     */
    public static ?\Closure $defaultCountStrategyForMinimalClasses = null;

    public static ?string $defaultIndexBy = null;

    /**
     * @var int<1,max>
     */
    public static int $defaultItemsPerPage = 50;
}
