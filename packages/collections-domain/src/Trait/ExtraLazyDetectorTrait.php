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

use Rekalogika\Domain\Collections\Internal\ExtraLazyDetector;

trait ExtraLazyDetectorTrait
{
    private ?bool $isExtraLazy = null;
    private ?bool $hasIndexBy = null;

    private function isExtraLazy(): bool
    {
        if ($this->isExtraLazy !== null) {
            return $this->isExtraLazy;
        }

        return $this->isExtraLazy = ExtraLazyDetector::isExtraLazy($this->collection);
    }

    private function hasIndexBy(): bool
    {
        if ($this->hasIndexBy !== null) {
            return $this->hasIndexBy;
        }

        return $this->hasIndexBy = ExtraLazyDetector::hasIndexBy($this->collection);
    }

}
