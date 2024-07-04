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

use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;

/**
 * @internal
 */
final class KeyTransformerUtil
{
    private function __construct()
    {
    }

    public static function transformInputToKey(
        ?KeyTransformer $keyTransformer,
        mixed $input
    ): int|string {
        $keyTransformer ??= Configuration::$defaultKeyTransformer;

        return $keyTransformer::transformToKey($input);
    }

    // /**
    //  * @template TKey of array-key
    //  * @template T
    //  * @param \Closure(mixed,T):bool $input
    //  * @return \Closure(TKey,T):bool
    //  */
    // public static function transformClosureTKeyTReturnsBool(
    //     ?KeyTransformer $keyTransformer,
    //     \Closure $input
    // ): \Closure {
    //     $keyTransformer ??= Configuration::$defaultKeyTransformer;

    //     return static function (mixed $key, mixed $value) use ($input, $keyTransformer): bool {
    //         return $input($keyTransformer::transformFromKey($key), $value);
    //     };
    // }

    // /**
    //  * @template TKey of array-key
    //  * @template T
    //  * @param \Closure(T,mixed):bool $input
    //  * @return \Closure(T,TKey):bool
    //  */
    // public static function transformClosureTTkeyReturnsBool(
    //     ?KeyTransformer $keyTransformer,
    //     \Closure $input
    // ): \Closure {
    //     $keyTransformer ??= Configuration::$defaultKeyTransformer;

    //     return static function (mixed $value, mixed $key) use ($input, $keyTransformer): bool {
    //         return $input($value, $keyTransformer::transformFromKey($key));
    //     };
    // }
}
