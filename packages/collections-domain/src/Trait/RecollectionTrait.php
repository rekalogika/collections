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

use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Contracts\Rekapager\PageInterface;
use Rekalogika\Domain\Collections\CountStrategy;
use Rekalogika\Domain\Collections\Exception\OverflowException;
use Rekalogika\Domain\Collections\Exception\UnsafeMethodCallException;
use Rekalogika\Domain\Collections\RecollectionConfiguration;
use Rekalogika\Rekapager\Doctrine\Collections\SelectableAdapter;
use Rekalogika\Rekapager\Keyset\KeysetPageable;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait RecollectionTrait
{
    /**
     * @var null|PageableInterface<TKey,T>
     */
    private ?PageableInterface $pageable = null;

    /**
     * @var array<TKey,T>|null
     */
    private ?array $itemsWithSafeguard = null;

    /**
     * @return PageableInterface<TKey,T>
     */
    private function getPageable(): PageableInterface
    {
        if ($this->pageable !== null) {
            return $this->pageable;
        }

        $adapter = new SelectableAdapter(
            collection: $this->collection,
            criteria: $this->criteria
        );

        $count = match ($this->countStrategy) {
            CountStrategy::Restrict => false,
            CountStrategy::Delegate => true,
            CountStrategy::Provided => $this->count,
        } ?? 0;

        $this->pageable = new KeysetPageable(
            adapter: $adapter,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );

        return $this->pageable;
    }

    /**
     * @return int<1,max>
     */
    private function getSoftLimit(): int
    {
        return $this->softLimit ?? RecollectionConfiguration::$defaultSoftLimit;
    }

    /**
     * @return int<1,max>
     */
    private function getHardLimit(): int
    {
        return $this->hardLimit ?? RecollectionConfiguration::$defaultHardLimit;
    }

    private function isStrict(): bool
    {
        return $this->strict ?? RecollectionConfiguration::$defaultStrict;
    }

    /**
     * @return array<TKey,T>
     */
    private function &getItemsWithSafeguard(): array
    {
        if ($this->isStrict()) {
            throw new UnsafeMethodCallException('The collection is in strict mode and does not allow unsafe methods at all.');
        }

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

    //
    // PageableInterface methods
    //

    /**
     * @return PageInterface<TKey,T>
     */
    final public function getPageByIdentifier(object $pageIdentifier): PageInterface
    {
        return $this->getPageable()->getPageByIdentifier($pageIdentifier);
    }

    /**
     * @return class-string
     */
    final public function getPageIdentifierClass(): string
    {
        return $this->getPageable()->getPageIdentifierClass();
    }

    /**
     * @return PageInterface<TKey,T>
     */
    final public function getFirstPage(): PageInterface
    {
        return $this->getPageable()->getFirstPage();
    }

    /**
     * @return PageInterface<TKey,T>
     */
    final public function getLastPage(): ?PageInterface
    {
        return $this->getPageable()->getLastPage();
    }

    /**
     * @return \Traversable<PageInterface<TKey,T>>
     */
    final public function getPages(): \Traversable
    {
        return $this->getPageable()->getPages();
    }

    /**
     * @return int<1,max>
     */
    final public function getItemsPerPage(): int
    {
        return $this->getPageable()->getItemsPerPage();
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    final public function withItemsPerPage(int $itemsPerPage): static
    {
        return $this->createFrom(itemsPerPage: $itemsPerPage);
    }

    /**
     * @return null|int<0,max>
     */
    final public function getTotalPages(): ?int
    {
        return $this->getPageable()->getTotalPages();
    }

    /**
     * @return null|int<0,max>
     */
    final public function getTotalItems(): ?int
    {
        return $this->getPageable()->getTotalItems();
    }
}
