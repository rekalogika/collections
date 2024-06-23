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

use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Contracts\Rekapager\PageInterface;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait PageableTrait
{
    /**
     * @return PageableInterface<TKey,T>
     */
    abstract private function getPageable(): PageableInterface;

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
    final public function getPages(?object $start = null): \Traversable
    {
        return $this->getPageable()->getPages($start);
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
        return $this->with(itemsPerPage: $itemsPerPage);
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
