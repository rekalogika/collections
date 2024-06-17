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
use Rekalogika\Domain\Collections\Exception\CountDisabledException;

trait CountableTrait
{
    /**
     * Unsafe, and we have a special case for this.
     *
     * @return int<0,max>
     * @throws CountDisabledException
     */
    final public function count(): int
    {
        if ($this->countStrategy === CountStrategy::Restrict) {
            throw new CountDisabledException();
        } elseif ($this->countStrategy === CountStrategy::Delegate) {
            $count = $this->getPageable()->getTotalItems();

            if (\is_int($count) && $count >= 0) {
                return $count;
            }
            return 0;
        }

        return $this->count ?? 0;
    }

    /**
     * Not part of the Countable interface. Used by the caller to refresh the
     * stored count by querying the collection.
     */
    final public function refreshCount(): void
    {
        $count = $this->getRealCount();

        if ($count >= 0) {
            $this->count = $count;
        }
    }
}
