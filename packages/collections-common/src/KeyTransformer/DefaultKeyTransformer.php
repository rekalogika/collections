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

namespace Rekalogika\Domain\Collections\Common\KeyTransformer;

use Rekalogika\Contracts\Collections\Exception\NotFoundException;

class DefaultKeyTransformer implements KeyTransformer
{
    private static ?self $instance = null;

    public static function create(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
    }

    public function transformToKey(mixed $key): int|string
    {
        if ($key instanceof \Stringable) {
            return (string) $key;
        } elseif (!\is_string($key) && !\is_int($key)) {
            throw new NotFoundException();
        }

        return $key;
    }
}
