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

use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Exception\OverflowException;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait ItemsWithSafeguardTrait
{
    /**
     * @var array<TKey,T>|null
     */
    private ?array $itemsWithSafeguard = null;

    /**
     * @return int<1,max>
     */
    private function getSoftLimit(): int
    {
        return $this->softLimit ?? Configuration::$defaultSoftLimit;
    }

    /**
     * @return int<1,max>
     */
    private function getHardLimit(): int
    {
        return $this->hardLimit ?? Configuration::$defaultHardLimit;
    }

    /**
     * @return array<TKey,T>
     */
    private function &getItemsWithSafeguard(): array
    {
        if ($this->itemsWithSafeguard !== null) {
            return $this->itemsWithSafeguard;
        }

        $firstPage = $this->getPageable()
            ->withItemsPerPage($this->getHardLimit())
            ->getFirstPage();

        if ($firstPage->getNextPage() !== null) {
            throw new OverflowException('The collection has more items than the hard safeguard limit.');
        }

        $items = iterator_to_array($firstPage);

        if (\count($items) > $this->getSoftLimit()) {
            @trigger_error("The collection has more items than the soft limit. Consider rewriting your code so that it can process the items in an efficient manner.", \E_USER_DEPRECATED);
        }

        // needs to separate the assignment & return for next() to work
        $this->itemsWithSafeguard = $items;

        return $this->itemsWithSafeguard;
    }
}
