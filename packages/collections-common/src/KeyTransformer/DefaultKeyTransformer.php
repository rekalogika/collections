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
use Symfony\Component\Uid\AbstractUid;

class DefaultKeyTransformer implements KeyTransformer
{
    public static function transformToKey(mixed $key): int|string
    {
        if ($key instanceof AbstractUid) {
            return $key->toBinary();
        } elseif (!\is_string($key) && !\is_int($key)) {
            throw new NotFoundException();
        }

        return $key;
    }
}
