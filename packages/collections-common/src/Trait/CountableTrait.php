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

use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Exception\CountDisabledException;

trait CountableTrait
{
    abstract private function getCountStrategy(): CountStrategy;

    /**
     * @return int<0,max>
     */
    abstract private function getRealCount(): int;

    /**
     * @return null|int<0,max>
     */
    abstract private function &getProvidedCount(): ?int;

    /**
     * @return int<0,max>
     * @throws CountDisabledException
     */
    final public function count(): int
    {
        if ($this->getCountStrategy() === CountStrategy::Restrict) {
            throw new CountDisabledException();
        } elseif ($this->getCountStrategy() === CountStrategy::Delegate) {
            $count = $this->getPageable()->getTotalItems();

            if (\is_int($count) && $count >= 0) {
                return $count;
            }
            return 0;
        }

        $count = $this->getProvidedCount();

        /** @psalm-suppress DocblockTypeContradiction */
        if ($count === null || $count < 0) {
            return 0;
        }

        return $count;
    }

    /**
     * Not part of the Countable interface. Used by the caller to refresh the
     * stored count by querying the collection.
     */
    final public function refreshCount(): void
    {
        $realCount = $this->getRealCount();

        if ($realCount >= 0) {
            $providedCount = &$this->getProvidedCount();
            $providedCount = $realCount;
        }
    }
}
