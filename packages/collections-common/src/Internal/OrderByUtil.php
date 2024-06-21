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

namespace Rekalogika\Domain\Collections\Common\Internal;

use Doctrine\Common\Collections\Order;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;
use Rekalogika\Domain\Collections\Common\Configuration;

/**
 * @internal
 */
final class OrderByUtil
{
    private function __construct()
    {
    }

    /**
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param null|non-empty-array<string,Order>|string $defaultOrderBy
     * @return non-empty-array<string,Order>
     */
    public static function normalizeOrderBy(
        array|string|null $orderBy = null,
        array|string|null $defaultOrderBy = null,
    ): array {
        if ($orderBy === null) {
            $orderBy = $defaultOrderBy ?? Configuration::$defaultOrderBy;
        }

        if (\is_string($orderBy)) {
            $orderBy = [$orderBy => Order::Ascending];
        }

        if (empty($orderBy)) {
            throw new UnexpectedValueException('The order by clause cannot be empty.');
        }

        return $orderBy;
    }
}
