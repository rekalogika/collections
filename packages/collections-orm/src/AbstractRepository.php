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
use Rekalogika\Collections\ORM\Configuration\RepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\MinimalRepositoryTrait;
use Rekalogika\Collections\ORM\Trait\QueryBuilderTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryTrait;
use Rekalogika\Contracts\Collections\Repository;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\ItemsWithSafeguardTrait;
use Rekalogika\Domain\Collections\Common\Trait\IteratorAggregateTrait;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;

/**
 * @template TKey of array-key
 * @template T of object
 * @implements Repository<TKey,T>
 */
abstract class AbstractRepository implements Repository
{
    /**
     * @use QueryBuilderTrait<array-key,T>
     */
    use QueryBuilderTrait;

    /**
     * @use PageableTrait<array-key,T>
     */
    use PageableTrait;

    /**
     * @use MinimalRepositoryTrait<array-key,T>
     */
    use MinimalRepositoryTrait;

    /**
     * @use ItemsWithSafeguardTrait<array-key,T>
     */
    use ItemsWithSafeguardTrait;

    /**
     * @use RepositoryTrait<array-key,T>
     */
    use RepositoryTrait;

    use CountableTrait;

    /**
     * @use IteratorAggregateTrait<array-key,T>
     */
    use IteratorAggregateTrait;

    /**
     * @var null|int<0,max>
     */
    private ?int $count = 0;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    private CountStrategy $countStrategy;
    private QueryBuilder $queryBuilder;

    /**
     * @var class-string<T>
     */
    private string $class;

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
        $this->countStrategy = $configuration->getCountStrategy();
        $this->softLimit = $configuration->getSoftLimit();
        $this->hardLimit = $configuration->getHardLimit();

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
