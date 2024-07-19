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

namespace Rekalogika\Domain\Collections\Common\Count;

use Rekalogika\Domain\Collections\Common\Exception\GettingCountUnsupportedException;
use Rekalogika\Domain\Collections\Common\Exception\SettingCountUnsupportedException;

class DisabledCountStrategy implements CountStrategy
{
    #[\Override]
    public function getCount(?\Countable $underlyingObject): int
    {
        throw new GettingCountUnsupportedException('Counting is disabled');
    }

    #[\Override]
    public function setCount(?\Countable $underlyingObject, int $count): void
    {
        throw new SettingCountUnsupportedException('Setting count is disabled');
    }
}
