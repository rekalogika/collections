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

namespace Rekalogika\Collections\ORM;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Rekalogika\Collections\ORM\Configuration\RepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryTrait;
use Rekalogika\Contracts\Collections\Repository;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\SafeCollectionTrait;
use Rekalogika\Domain\Collections\CriteriaRecollection;

/**
 * @template TKey of array-key
 * @template T of object
 * @implements Repository<TKey,T>
 */
abstract class AbstractRepository implements Repository
{
    /**
     * @use QueryBuilderPageableTrait<array-key,T>
     */
    use QueryBuilderPageableTrait;

    /**
     * @use RepositoryTrait<array-key,T>
     */
    use RepositoryTrait;

    /**
     * @use SafeCollectionTrait<array-key,T>
     */
    use SafeCollectionTrait;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    private readonly CountStrategy $count;
    private readonly QueryBuilder $queryBuilder;
    private readonly ?string $indexBy;

    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;

    /**
     * @var class-string<T>
     */
    private readonly string $class;

    /**
     * @var null|int<1,max>
     */
    private readonly ?int $softLimit;

    /**
     * @var null|int<1,max>
     */
    private readonly ?int $hardLimit;

    final public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $configuration = $this->configure();
        $this->class = $configuration->getClass();
        $this->itemsPerPage = $configuration->getItemsPerPage();
        $this->count = $configuration->getCountStrategy();
        $this->indexBy = $configuration->getIndexBy();
        $this->softLimit = $configuration->getSoftLimit();
        $this->hardLimit = $configuration->getHardLimit();
        $this->orderBy = $configuration->getOrderBy();

        // set query builder
        $criteria = Criteria::create()->orderBy($configuration->getOrderBy());
        $this->queryBuilder = $this
            ->createQueryBuilder('e', 'e.' . $configuration->getIndexBy())
            ->addCriteria($criteria);
    }

    /**
     * @return RepositoryConfiguration<T>
     */
    abstract protected function configure(): RepositoryConfiguration;

    private function getCountStrategy(): CountStrategy
    {
        return $this->count;
    }

    /**
     * @return null|int<1,max>
     */
    private function getSoftLimit(): ?int
    {
        return $this->softLimit;
    }

    /**
     * @return null|int<1,max>
     */
    private function getHardLimit(): ?int
    {
        return $this->hardLimit;
    }

    /**
     * @return class-string<T>
     */
    private function getClass(): string
    {
        /** @var class-string<T> */
        return $this->class;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function withItemsPerPage(int $itemsPerPage): static
    {
        /** @psalm-suppress UnsafeGenericInstantiation */
        $instance = new static(entityManager: $this->entityManager);
        $instance->itemsPerPage = $itemsPerPage;

        return $instance;
    }

    //
    // accessors for subclasses
    //

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
    protected function applyCriteria(
        Criteria $criteria,
        ?string $instanceId = null,
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
            itemsPerPage: $this->itemsPerPage,
            count: $count,
            softLimit: $this->softLimit,
            hardLimit: $this->hardLimit,
        );
    }
}
