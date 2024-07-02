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
use Rekalogika\Collections\ORM\Configuration\MinimalRepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\MinimalRepositoryTrait;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryDxTrait;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;

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

    final public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $configuration = $this->configure();
        $this->class = $configuration->getClass();
        $this->itemsPerPage = $configuration->getItemsPerPage();
        $this->count = $configuration->getCountStrategy();
        $this->indexBy = $configuration->getIndexBy();
        $this->orderBy = $configuration->getOrderBy();

        // set query builder
        $criteria = Criteria::create()->orderBy($this->orderBy);
        $this->queryBuilder = $this
            ->createQueryBuilder('e', 'e.' . $configuration->getIndexBy())
            ->addCriteria($criteria);
    }

    /**
     * @return MinimalRepositoryConfiguration<T>
     */
    abstract protected function configure(): MinimalRepositoryConfiguration;

    /**
     * @return class-string<T>
     */
    private function getClass(): string
    {
        /** @var class-string<T> */
        return $this->class;
    }

    private function getCountStrategy(): ?CountStrategy
    {
        return $this->count;
    }

    /**
     * @return null|int<1,max>
     */
    // @phpstan-ignore-next-line
    private function getSoftLimit(): ?int
    {
        return null;
    }

    /**
     * @return null|int<1,max>
     */
    // @phpstan-ignore-next-line
    private function getHardLimit(): ?int
    {
        return null;
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
