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

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Domain\Collections\CriteriaPageable;
use Rekalogika\Domain\Collections\MinimalCriteriaRecollection;

/**
 * @template TKey of array-key
 * @template T
 */
trait MinimalRecollectionDxTrait
{
    /**
     * @return non-empty-array<string,Order>
     */
    abstract private function getOrderBy(): array;

    final protected function createCriteria(): Criteria
    {
        return clone $this->criteria;
    }

    /**
     * @param int<1,max>|null $itemsPerPage
     * @return MinimalCriteriaRecollection<TKey,T>
     */
    final protected function createCriteriaRecollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        ?KeyTransformer $keyTransformer = null,
        ?Pagination $pagination = null,
    ): MinimalCriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->getOrderBy());
        }

        $indexBy ??= $this->indexBy;
        $itemsPerPage ??= $this->itemsPerPage;
        $keyTransformer ??= $this->keyTransformer;
        $pagination ??= $this->pagination;

        return MinimalCriteriaRecollection::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            indexBy: $indexBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
            keyTransformer: $keyTransformer,
            pagination: $pagination,
        );
    }

    /**
     * @param int<1,max>|null $itemsPerPage
     * @return PageableRecollection<TKey,T>
     */
    final protected function createCriteriaPageable(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        ?Pagination $pagination = null,
    ): PageableRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->getOrderBy());
        }

        $indexBy ??= $this->indexBy;
        $itemsPerPage ??= $this->itemsPerPage;
        $pagination ??= $this->pagination;

        return CriteriaPageable::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            indexBy: $indexBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
            pagination: $pagination,
        );
    }
}
