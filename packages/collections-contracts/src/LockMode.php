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

use Doctrine\DBAL\LockMode as DBALLockMode;

enum LockMode
{
    case Read;
    case Write;

    /**
     * @return DBALLockMode::PESSIMISTIC_WRITE|DBALLockMode::PESSIMISTIC_READ
     */
    public function toDoctrineLockMode(): mixed
    {
        return match ($this) {
            self::Read => \Doctrine\DBAL\LockMode::PESSIMISTIC_READ,
            self::Write => \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE,
        };
    }
}
