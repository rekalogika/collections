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

namespace Rekalogika\Domain\Collections\Trait;

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Domain\Collections\Internal\ExtraLazyDetector;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait SafetyCheckTrait
{
    private ?bool $isSafe = null;
    private ?bool $isSafeWithIndex = null;

    /**
     * @return ReadableCollection<TKey,T>
     */
    abstract private function getRealCollection(): ReadableCollection;

    private function isSafe(): bool
    {
        if ($this->isSafe !== null) {
            return $this->isSafe;
        }

        return $this->isSafe = ExtraLazyDetector::isSafe($this->getRealCollection());
    }

    private function isSafeWithIndex(): bool
    {
        if ($this->isSafeWithIndex !== null) {
            return $this->isSafeWithIndex;
        }

        return $this->isSafeWithIndex = ExtraLazyDetector::isSafeWithIndex($this->getRealCollection());
    }
}
