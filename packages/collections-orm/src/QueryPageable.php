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
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
use Rekalogika\Domain\Collections\Common\Trait\RefreshCountTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements PageableRecollection<TKey,T>
 */
class QueryPageable implements PageableRecollection
{
    /** @use QueryBuilderPageableTrait<TKey,T> */
    use QueryBuilderPageableTrait;

    /** @use PageableTrait<TKey,T> */
    use PageableTrait;

    use RefreshCountTrait;

    private readonly ?string $indexBy;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        private readonly ?CountStrategy $count = null,
    ) {
        $this->indexBy = $indexBy ?? Configuration::$defaultIndexBy;
        $this->itemsPerPage = $itemsPerPage ?? Configuration::$defaultItemsPerPage;
    }

    private function getCountStrategy(): CountStrategy
    {
        return $this->count ?? ParameterUtil::getDefaultCountStrategyForMinimalClasses();
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function withItemsPerPage(int $itemsPerPage): static
    {
        $instance = clone $this;
        $instance->itemsPerPage = $itemsPerPage;
        $instance->pageable = null;

        return $instance;
    }

    final public function getQueryBuilder(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }

    /**
     * @param \Closure(QueryBuilder):void $function
     */
    final public function updateQueryBuilder(\Closure $function): static
    {
        $instance = clone $this;
        $function($instance->queryBuilder);

        return $instance;
    }

    final protected function withQueryBuilder(QueryBuilder $queryBuilder): static
    {
        $instance = clone $this;
        $instance->queryBuilder = $queryBuilder;

        return $instance;
    }

    /**
     * @return self<TKey,T>
     */
    final protected function createQueryRecollection(
        QueryBuilder $queryBuilder,
        ?string $indexBy = null,
        ?CountStrategy $count = null,
    ): self {
        /**
         * @var self<TKey,T>
         * @phpstan-ignore-next-line
         */
        return new QueryRecollection(
            queryBuilder: $queryBuilder,
            indexBy: $indexBy ?? $this->indexBy,
            count: $count,
            itemsPerPage: $this->itemsPerPage,
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
