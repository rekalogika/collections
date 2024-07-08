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
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryDxTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryTrait;
use Rekalogika\Contracts\Collections\Repository;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\OrderByUtil;
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

    private readonly QueryBuilder $queryBuilder;

    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;

    /**
     * @param class-string<T> $class
     * @param int<1,max> $itemsPerPage
     * @param int<1,max> $softLimit
     * @param int<1,max> $hardLimit
     * @param null|non-empty-array<string,Order>|string $orderBy
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $class,
        private readonly string $indexBy = 'id',
        array|string|null $orderBy = null,
        private int $itemsPerPage = 50,
        private readonly ?CountStrategy $count = null,
        private readonly ?int $softLimit = null,
        private readonly ?int $hardLimit = null,
        private readonly ?KeyTransformer $keyTransformer = null,
    ) {
        $this->orderBy = OrderByUtil::normalizeOrderBy($orderBy);

        // set query builder
        $criteria = Criteria::create()->orderBy($this->orderBy);
        $this->queryBuilder = $this
            ->createQueryBuilder('e', 'e.' . $indexBy)
            ->addCriteria($criteria);
    }

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
        $instance = clone $this;
        $instance->itemsPerPage = $itemsPerPage;

        return $instance;
    }
}
