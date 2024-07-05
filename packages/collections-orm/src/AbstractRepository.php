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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Rekalogika\Collections\ORM\Configuration\RepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryDxTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryTrait;
use Rekalogika\Contracts\Collections\Repository;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;
use Rekalogika\Domain\Collections\Common\Trait\SafeCollectionTrait;

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
     * @use RepositoryDxTrait<array-key,T>
     */
    use RepositoryDxTrait;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    private readonly ?CountStrategy $count;
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

    private readonly ?KeyTransformer $keyTransformer;

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
        $this->keyTransformer = $configuration->getKeyTransformer();

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

    private function getCountStrategy(): ?CountStrategy
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
}
