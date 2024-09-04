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
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Count\DisabledCountStrategy;
use Rekalogika\Domain\Collections\Common\Count\SafeDelegatedCountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\DefaultKeyTransformer;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;

/**
 * @internal
 */
final class ParameterUtil
{
    private function __construct() {}

    public static function getDefaultCountStrategyForFullClasses(): CountStrategy
    {
        $closure = Configuration::$defaultCountStrategyForFullClasses
            ?? fn(): CountStrategy => new SafeDelegatedCountStrategy();

        return $closure();
    }

    public static function getDefaultCountStrategyForMinimalClasses(): CountStrategy
    {
        $closure = Configuration::$defaultCountStrategyForMinimalClasses
            ?? fn(): CountStrategy => new DisabledCountStrategy();

        return $closure();
    }

    public static function getDefaultKeyTransformer(): KeyTransformer
    {
        $closure = Configuration::$defaultKeyTransformer
            ?? fn(): KeyTransformer => DefaultKeyTransformer::create();

        return $closure();
    }

    public static function transformInputToKey(
        ?KeyTransformer $keyTransformer,
        mixed $input,
    ): int|string {
        $keyTransformer ??= self::getDefaultKeyTransformer();

        return $keyTransformer->transformToKey($input);
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

        if ($orderBy === []) {
            throw new UnexpectedValueException('The order by clause cannot be empty.');
        }

        return $orderBy;
    }
}
