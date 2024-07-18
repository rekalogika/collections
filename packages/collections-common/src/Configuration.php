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
     * If the result of a count exceeds this number, a deprecation notice will
     * be emitted. Used by `SafeDelegatedCountStrategy`. Not applicable with
     * other count strategies.
     *
     * @var int<1,max>
     */
    public static int $defaultCountSoftLimit = 5000;

    /**
     * If the result of a count exceeds this number, an exception will be
     * thrown. Used by `SafeDelegatedCountStrategy`. Not applicable with other
     * count strategies.
     *
     * @var int<1,max>
     */
    public static int $defaultCountHardLimit = 50000;

    /**
     * If the duration of a count operation exceeds this number of seconds, a
     * deprecation notice will be emitted. Used by `SafeDelegatedCountStrategy`,
     * Not applicable with other count strategies.
     */
    public static float $defaultCountDurationThreshold = 2;

    /**
     * The default order by clause for the collection.
     *
     * @var non-empty-array<string,Order>
     */
    public static array $defaultOrderBy = ['id' => Order::Descending];

    /**
     * The default key transformer for the collection.
     *
     * @var null|\Closure(): KeyTransformer
     */
    public static ?\Closure $defaultKeyTransformer = null;

    /**
     * Default count strategy for full, non-minimal, classes.
     *
     * @var null|\Closure(): CountStrategy
     */
    public static ?\Closure $defaultCountStrategyForFullClasses = null;

    /**
     * Default count strategy for minimal classes.
     *
     * @var null|\Closure(): CountStrategy
     */
    public static ?\Closure $defaultCountStrategyForMinimalClasses = null;

    /**
     * Default columns used for indexing.
     */
    public static ?string $defaultIndexBy = null;

    /**
     * Default items per page for pagination.
     *
     * @var int<1,max>
     */
    public static int $defaultItemsPerPage = 50;
}
