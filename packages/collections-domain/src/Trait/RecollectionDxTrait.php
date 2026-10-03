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
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Domain\Collections\CriteriaPageable;
use Rekalogika\Domain\Collections\CriteriaRecollection;

/**
 * @template TKey of array-key
 * @template T
 */
trait RecollectionDxTrait
{
    /**
     * @return non-empty-array<string,\SortDirection>
     */
    abstract private function getOrderBy(): array;

    final protected function createCriteria(): Criteria
    {
        return clone $this->criteria;
    }

    /**
     * Narrows down this collection using the supplied criteria. The orderings,
     * first result & max results of the supplied criteria take precedence
     * over the current ones.
     *
     * @return CriteriaRecollection<TKey,T>
     */
    #[\Override]
    final public function matching(Criteria $criteria): CriteriaRecollection
    {
        $newCriteria = $this->createCriteria();

        $where = $criteria->getWhereExpression();
        if ($where !== null) {
            $newCriteria->andWhere($where);
        }

        $orderings = $criteria->getOrderings();
        if ($orderings !== []) {
            $newCriteria->orderBy($orderings);
        }

        $firstResult = $criteria->getFirstResult();
        if ($firstResult !== null) {
            $newCriteria->setFirstResult($firstResult);
        }

        $maxResults = $criteria->getMaxResults();
        if ($maxResults !== null) {
            $newCriteria->setMaxResults($maxResults);
        }

        return $this->createCriteriaRecollection($newCriteria);
    }

    /**
     * @param int<1,max>|null $itemsPerPage
     * @return CriteriaRecollection<TKey,T>
     */
    final protected function createCriteriaRecollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        ?KeyTransformer $keyTransformer = null,
        ?Pagination $pagination = null,
    ): CriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->getOrderings() === []) {
            $criteria = $criteria->orderBy($this->getOrderBy());
        }

        $indexBy ??= $this->indexBy;
        $itemsPerPage ??= $this->itemsPerPage;
        $keyTransformer ??= $this->keyTransformer;
        $pagination ??= $this->pagination;

        return CriteriaRecollection::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            indexBy: $indexBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
            softLimit: $this->softLimit,
            hardLimit: $this->hardLimit,
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
        if ($criteria->getOrderings() === []) {
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
