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

use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Exception\GettingCountUnsupportedException;
use Rekalogika\Domain\Collections\Common\Exception\SettingCountUnsupportedException;

class ConditionalDelegatedCountStrategy implements CountStrategy
{
    public function __construct(
        private ?int $softLimit = null,
        private ?int $hardLimit = null,
        private ?float $durationThreshold = null,
    ) {
    }

    public function getCount(?\Countable $underlyingObject): int
    {
        if ($underlyingObject === null) {
            throw new GettingCountUnsupportedException('The underlying object is not provided');
        }

        $softLimit = $this->softLimit ?? Configuration::$defaultCountSoftLimit;
        $hardLimit = $this->hardLimit ?? Configuration::$defaultCountHardLimit;
        $durationThreshold = $this->durationThreshold ?? Configuration::$defaultCountDurationThreshold;

        $start = microtime(true);
        $result = \count($underlyingObject);
        $duration = microtime(true) - $start;

        if ($result > $hardLimit) {
            throw new GettingCountUnsupportedException(sprintf('The count exceeds the threshold of %d. You should refactor and use other counting strategy. Count duration: %d s', $hardLimit, $duration));
        } elseif ($result > $softLimit) {
            @trigger_error(sprintf('The count exceeds the warning threshold of %d. As it might impact performance, you should refactor and use other counting strategy. Count duration: %d s.', $softLimit, $duration), \E_USER_DEPRECATED);
        } elseif ($duration > $durationThreshold) {
            @trigger_error(sprintf('The count duration is %d s. You should consider refactoring and using other counting strategy.', $duration), \E_USER_DEPRECATED);
        }

        return $result;
    }

    public function setCount(?\Countable $underlyingObject, int $count): void
    {
        throw new SettingCountUnsupportedException('Setting count is disabled');
    }
}
