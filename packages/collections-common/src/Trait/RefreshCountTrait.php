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

namespace Rekalogika\Domain\Collections\Common\Trait;

use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Exception\InvalidCountException;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

trait RefreshCountTrait
{
    abstract private function getCountStrategy(): ?CountStrategy;
    abstract private function getUnderlyingCountable(): ?\Countable;

    /**
     * @return int<0,max>
     */
    private function getCount(): int
    {
        $countStrategy = $this->getCountStrategy() ?? ParameterUtil::getDefaultCountStrategy();

        $result = $countStrategy->getCount($this->getUnderlyingCountable());

        if ($result >= 0) {
            return $result;
        }

        throw new InvalidCountException('Invalid count');
    }

    final public function refreshCount(): void
    {
        $countStrategy = $this->getCountStrategy() ?? ParameterUtil::getDefaultCountStrategy();

        $realCount = \count($this->getUnderlyingCountable());
        $countStrategy->setCount($this->getUnderlyingCountable(), $realCount);
    }
}
