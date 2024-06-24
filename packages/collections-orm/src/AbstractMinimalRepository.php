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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Rekalogika\Collections\ORM\Configuration\MinimalRepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\MinimalRepositoryTrait;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Domain\Collections\Common\CountStrategy;

/**
 * @template TKey of array-key
 * @template T of object
 * @implements MinimalRepository<TKey,T>
 */
abstract class AbstractMinimalRepository implements MinimalRepository
{
    /**
     * @use QueryBuilderPageableTrait<array-key,T>
     */
    use QueryBuilderPageableTrait;

    /**
     * @use MinimalRepositoryTrait<array-key,T>
     */
    use MinimalRepositoryTrait;

    private ?int $count = 0;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    private readonly CountStrategy $countStrategy;
    private readonly QueryBuilder $queryBuilder;
    private readonly ?string $indexBy;

    /**
     * @var class-string<T>
     */
    private readonly string $class;

    final public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $configuration = $this->configure();
        $this->class = $configuration->getClass();
        $this->itemsPerPage = $configuration->getItemsPerPage();
        $this->countStrategy = $configuration->getCountStrategy();
        $this->indexBy = $configuration->getIndexBy();

        // set query builder
        $criteria = Criteria::create()->orderBy($configuration->getOrderBy());
        $this->queryBuilder = $this
            ->createQueryBuilder('e', 'e.' . $configuration->getIndexBy())
            ->addCriteria($criteria);
    }

    /**
     * @return MinimalRepositoryConfiguration<T>
     */
    abstract protected function configure(): MinimalRepositoryConfiguration;

    /**
     * @param int<1,max> $itemsPerPage
     */
    protected function with(
        ?int $itemsPerPage = null,
    ): static {
        $clone = clone $this;
        $clone->itemsPerPage = $itemsPerPage ?? $this->itemsPerPage;

        return $clone;
    }

    /**
     * @return class-string<T>
     */
    private function getClass(): string
    {
        /** @var class-string<T> */
        return $this->class;
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

}
