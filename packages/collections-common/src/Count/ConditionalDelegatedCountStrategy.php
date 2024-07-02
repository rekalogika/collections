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

class ConditionalDelegatedCountStrategy implements CountStrategy
{
    public function __construct(
        private ?int $softLimit = 5000,
        private ?int $hardLimit = null,
        private ?float $durationThreshold = 1,
    ) {
    }

    public function getCount(?\Countable $underlyingObject): int
    {
        if ($underlyingObject === null) {
            throw new GettingCountUnsupportedException('The underlying object is not provided');
        }

        $start = microtime(true);
        $result = \count($underlyingObject);
        $duration = microtime(true) - $start;

        if ($this->hardLimit !== null && $result > $this->hardLimit) {
            throw new GettingCountUnsupportedException(sprintf('The count exceeds the threshold of %d. You should refactor and use other counting strategy. Count duration: %d s', $this->hardLimit, $duration));
        } elseif ($this->softLimit !== null && $result > $this->softLimit) {
            @trigger_error(sprintf('The count exceeds the warning threshold of %d. As it might impact performance, you should refactor and use other counting strategy. Count duration: %d s.', $this->softLimit, $duration), \E_USER_DEPRECATED);
        } elseif ($duration > $this->durationThreshold) {
            @trigger_error(sprintf('The count duration is %d s. You should consider refactoring and using other counting strategy.', $duration), \E_USER_DEPRECATED);
        }

        return $result;
    }

    public function setCount(?\Countable $underlyingObject, int $count): void
    {
        throw new SettingCountUnsupportedException('Setting count is disabled');
    }
}
