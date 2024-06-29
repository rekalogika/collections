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

trait RefreshableCountTrait
{
    abstract private function getCountStrategy(): CountStrategy;
    abstract private function getUnderlyingCountable(): ?\Countable;

    final public function refreshCount(): void
    {
        $realCount = \count($this->getUnderlyingCountable());
        $this->getCountStrategy()->setCount($this->getUnderlyingCountable(), $realCount);
    }
}
