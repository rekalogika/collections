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

trait CountableTrait
{
    abstract private function getCountStrategy(): CountStrategy;
    abstract private function getUnderlyingCountable(): ?\Countable;

    /**
     * @return int<0,max>
     */
    final public function count(): int
    {
        $result = $this->getCountStrategy()->getCount($this->getUnderlyingCountable());

        if ($result >= 0) {
            return $result;
        }

        throw new InvalidCountException('Invalid count');
    }

    /**
     * Not part of the Countable interface. Used by the caller to refresh the
     * stored count by querying the collection.
     */
    final public function refreshCount(): void
    {
        $realCount = \count($this->getUnderlyingCountable());
        $this->getCountStrategy()->setCount($this->getUnderlyingCountable(), $realCount);
    }
}
