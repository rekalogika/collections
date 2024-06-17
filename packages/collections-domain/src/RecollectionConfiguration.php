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

use Doctrine\Common\Collections\Order;

final class RecollectionConfiguration
{
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
     * If true, the collection will always throw an exception if an unsafe
     * method is called, ignoring the hard limit.
     */
    public static bool $defaultStrict = false;

    /**
     * The default order by clause for the collection.
     *
     * @var array<string,Order>
     */
    public static array $defaultOrderBy = ['id' => Order::Descending];
}
