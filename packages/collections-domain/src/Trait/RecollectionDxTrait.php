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
use Rekalogika\Domain\Collections\CriteriaPageable;
use Rekalogika\Domain\Collections\CriteriaRecollection;

/**
 * @template TKey of array-key
 * @template T
 */
trait RecollectionDxTrait
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
     * @return CriteriaRecollection<TKey,T>
     */
    final protected function createCriteriaRecollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
    ): CriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->getOrderBy());
        }

        return CriteriaRecollection::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
            softLimit: $this->softLimit,
            hardLimit: $this->hardLimit,
        );
    }

    /**
     * @return PageableRecollection<TKey,T>
     */
    final protected function createCriteriaPageable(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
    ): PageableRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->getOrderBy());
        }

        return CriteriaPageable::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );
    }
}
