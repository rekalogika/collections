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

final class PrecountingStrategy implements CountStrategy
{
    /**
     * @param ?int<0,max> $count
     */
    public function __construct(private ?int &$count) {}

    #[\Override]
    public function getCount(?\Countable $underlyingObject): int
    {
        return $this->count ?? 0;
    }

    #[\Override]
    public function setCount(?\Countable $underlyingObject, int $count): void
    {
        $this->count = $count;
    }
}
