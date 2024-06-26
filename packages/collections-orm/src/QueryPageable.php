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

use Doctrine\ORM\QueryBuilder;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Count\RestrictedCountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements PageableInterface<TKey,T>
 */
class QueryPageable implements PageableInterface
{
    /** @use QueryBuilderPageableTrait<TKey,T> */
    use QueryBuilderPageableTrait;

    /** @use PageableTrait<TKey,T> */
    use PageableTrait;

    private readonly CountStrategy $count;

    /**
     * @param int<1,max> $itemsPerPage
     */
    final public function __construct(
        private QueryBuilder $queryBuilder,
        private readonly int $itemsPerPage = 50,
        private readonly ?string $indexBy = null,
        ?CountStrategy $count = null,
    ) {
        $this->count = $count ?? new RestrictedCountStrategy();
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function withItemsPerPage(int $itemsPerPage): static
    {
        /** @psalm-suppress UnsafeGenericInstantiation */
        return new static(
            queryBuilder: $this->queryBuilder,
            itemsPerPage: $itemsPerPage,
            count: $this->count,
        );
    }

    final protected function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @return self<TKey,T>
     */
    final protected function createQueryCollection(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): self {
        /**
         * @var self<TKey,T>
         * @phpstan-ignore-next-line
         */
        return new QueryCollection(
            queryBuilder: $queryBuilder,
            indexBy: $indexBy ?? $this->indexBy,
            count: $count,
            itemsPerPage: $this->itemsPerPage,
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
