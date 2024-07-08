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
use Rekalogika\Domain\Collections\Common\Count\ConditionalDelegatedCountStrategy;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;

/**
 * @internal
 */
final class ParameterUtil
{
    private function __construct()
    {
    }

    public static function getDefaultCountStrategy(): CountStrategy
    {
        if (Configuration::$defaultCountStrategy === null) {
            $countStrategy = fn (): CountStrategy => new ConditionalDelegatedCountStrategy();
        } else {
            $countStrategy = Configuration::$defaultCountStrategy;
        }

        return $countStrategy();
    }

    public static function transformInputToKey(
        ?KeyTransformer $keyTransformer,
        mixed $input
    ): int|string {
        $keyTransformer ??= Configuration::$defaultKeyTransformer;

        return $keyTransformer::transformToKey($input);
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
