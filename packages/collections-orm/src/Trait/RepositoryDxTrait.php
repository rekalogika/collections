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
use Rekalogika\Collections\ORM\QueryPageable;
use Rekalogika\Collections\ORM\QueryRecollection;
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\PageableRecollection;
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
        if ($this->entityManager !== null) {
            return $this->entityManager;
        }

        $entityManager = $this->managerRegistry->getManagerForClass($this->getClass());

        if (null === $entityManager) {
            throw new InvalidArgumentException(sprintf('Entity manager not found for class "%s"', $this->getClass()));
        }

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new InvalidArgumentException(sprintf('Manager for class "%s" is not an instance of EntityManagerInterface', $this->getClass()));
        }

        return $this->entityManager = $entityManager;
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
    final protected function createCriteriaRecollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): CriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
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
     * @return PageableRecollection<TKey,T>
     */
    final protected function createCriteriaPageable(
        Criteria $criteria,
        ?string $instanceId = null,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): PageableRecollection {
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
     * @return QueryRecollection<TKey,T>
     */
    final protected function createQueryRecollection(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): QueryRecollection {
        /** @var QueryRecollection<TKey,T> */
        return new QueryRecollection(
            queryBuilder: $queryBuilder,
            indexBy: $indexBy ?? $this->indexBy,
            count: $count,
            itemsPerPage: $this->itemsPerPage,
            softLimit: $this->getSoftLimit(),
            hardLimit: $this->getHardLimit(),
        );
    }

    /**
     * @return PageableRecollection<TKey,T>
     */
    final protected function createQueryPageable(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): PageableRecollection {
        /**
         * @var PageableRecollection<TKey,T>
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
