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

namespace Rekalogika\Contracts\Collections;

enum LockMode
{
    case Read;
    case Write;

    /**
     * @return 2|4
     */
    public function toDoctrineLockMode(): int
    {
        return match ($this) {
            self::Read => \Doctrine\DBAL\LockMode::PESSIMISTIC_READ,
            self::Write => \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE,
        };
    }
}
