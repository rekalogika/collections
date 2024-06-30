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

namespace Rekalogika\Collections\ORM\Trait;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Rekalogika\Collections\ORM\QueryCollection;
use Rekalogika\Collections\ORM\QueryPageable;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\CriteriaPageable;
use Rekalogika\Domain\Collections\CriteriaRecollection;

/**
 * @template TKey of array-key
 * @template T of object
 *
 * @internal
 */
trait RepositoryDxTrait
{
    final protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    final protected function createQueryBuilder(
        string $alias,
        ?string $indexBy = null
    ): QueryBuilder {
        return $this->getEntityManager()->createQueryBuilder()
            ->select($alias)
            ->from($this->getClass(), $alias, $indexBy);
    }

    /**
     * @return ObjectRepository<T>&Selectable<TKey,T>
     */
    final protected function getDoctrineRepository(): ObjectRepository&Selectable
    {
        return $this->getEntityManager()->getRepository($this->getClass());
    }

    /**
     * @return CriteriaRecollection<TKey,T>
     */
    final protected function createCriteriaCollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): CriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if (\count($criteria->orderings()) === 0) {
            $criteria = $criteria->orderBy($this->orderBy);
        }

        /**
         * @var CriteriaRecollection<TKey,T>
         * @psalm-suppress InvalidArgument
         */
        return CriteriaRecollection::create(
            collection: $this->getDoctrineRepository(),
            criteria: $criteria,
            instanceId: $instanceId,
            indexBy: $indexBy ?? $this->indexBy,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
            softLimit: $this->getSoftLimit(),
            hardLimit: $this->getHardLimit(),
        );
    }

    /**
     * @return PageableInterface<TKey,T>
     */
    final protected function createCriteriaPageable(
        Criteria $criteria,
        ?string $instanceId = null,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): PageableInterface {
        return CriteriaPageable::create(
            collection: $this->getDoctrineRepository(),
            criteria: $criteria,
            instanceId: $instanceId,
            indexBy: $indexBy ?? $this->indexBy,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );
    }

    /**
     * @return QueryCollection<TKey,T>
     */
    final protected function createQueryCollection(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): QueryCollection {
        /** @var QueryCollection<TKey,T> */
        return new QueryCollection(
            queryBuilder: $queryBuilder,
            indexBy: $indexBy ?? $this->indexBy,
            count: $count,
            itemsPerPage: $this->itemsPerPage,
            softLimit: $this->getSoftLimit(),
            hardLimit: $this->getHardLimit(),
        );
    }

    /**
     * @return PageableInterface<TKey,T>
     */
    final protected function createQueryPageable(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): PageableInterface {
        /**
         * @var PageableInterface<TKey,T>
         * @phpstan-ignore-next-line
         */
        return new QueryPageable(
            queryBuilder: $queryBuilder,
            itemsPerPage: $this->itemsPerPage,
            indexBy: $indexBy ?? $this->indexBy,
            count: $count,
        );
    }
}
