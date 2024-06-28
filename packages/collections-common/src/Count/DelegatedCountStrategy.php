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

class DelegatedCountStrategy implements CountStrategy
{
    public function getCount(?\Countable $underlyingObject): int
    {
        if (!$underlyingObject instanceof \Countable) {
            throw new GettingCountUnsupportedException('The underlying object is not a Countable object');
        }

        return \count($underlyingObject);
    }

    public function setCount(?\Countable $underlyingObject, int $count): void
    {
        throw new SettingCountUnsupportedException('Setting count is disabled');
    }
}
