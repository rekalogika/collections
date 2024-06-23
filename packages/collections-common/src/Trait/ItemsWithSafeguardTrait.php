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

use Rekalogika\Contracts\Collections\Exception\OverflowException;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Configuration;

/**
 * @template TKey of array-key
 * @template-covariant T
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
     * @return PageableInterface<TKey,T>
     */
    abstract private function getPageable(): PageableInterface;

    /**
     * @return null|int<1,max>
     */
    abstract private function getSoftLimit(): ?int;

    /**
     * @return null|int<1,max>
     */
    abstract private function getHardLimit(): ?int;

    /**
     * @return array<TKey,T>
     */
    private function &getItemsWithSafeguard(): array
    {
        if ($this->itemsWithSafeguard !== null) {
            return $this->itemsWithSafeguard;
        }

        $hardLimit = $this->getHardLimit() ?? Configuration::$defaultHardLimit;

        $firstPage = $this->getPageable()
            ->withItemsPerPage($hardLimit)
            ->getFirstPage();

        if ($firstPage->getNextPage() !== null) {
            throw new OverflowException('The collection has more items than the hard safeguard limit.');
        }

        /**
         * @var array<TKey,T>
         */
        $items = iterator_to_array($firstPage);

        $softLimit = $this->getSoftLimit() ?? Configuration::$defaultSoftLimit;

        if (\count($items) > $softLimit) {
            @trigger_error("The collection has more items than the soft limit. Consider rewriting your code so that it can process the items in an efficient manner.", \E_USER_DEPRECATED);
        }

        // needs to separate the assignment & return for next() to work
        $this->itemsWithSafeguard = $items;

        return $this->itemsWithSafeguard;
    }
}
