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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Rekalogika\Contracts\Collections\Exception\OverflowException;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Configuration;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait SafeCollectionTrait
{
    /**
     * @var Collection<TKey,T>|null
     */
    private ?Collection $safeCollection = null;

    /**
     * @var Collection<TKey,T>|null
     */
    private ?Collection $newCollection = null;

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

    private function ensureSafety(): void
    {
        $this->getSafeCollection();
    }

    /**
     * @return null|Collection<TKey,T>
     */
    private function getSafeCollectionOrNull(): ?Collection
    {
        return $this->safeCollection;
    }

    /**
     * @return Collection<TKey,T>
     */
    private function getSafeCollection(): Collection
    {
        if ($this->safeCollection !== null) {
            return $this->safeCollection;
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

        return $this->safeCollection = new ArrayCollection($items);
    }

    /**
     * @return null|Collection<TKey,T>
     */
    private function getNewCollectionOrNull(): ?Collection
    {
        return $this->newCollection;
    }

    /**
     * @return Collection<TKey,T>
     */
    private function getNewCollection(): Collection
    {
        return $this->newCollection ??= new ArrayCollection();
    }
}
