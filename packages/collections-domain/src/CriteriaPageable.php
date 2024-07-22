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

namespace Rekalogika\Domain\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
use Rekalogika\Domain\Collections\Common\Trait\RefreshCountTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements PageableRecollection<TKey,T>
 */
class CriteriaPageable implements PageableRecollection
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    /** @use PageableTrait<TKey,T> */
    use PageableTrait;

    use RefreshCountTrait;

    /**
     * @var null|\WeakMap<object,array<string,self<array-key,mixed>>>
     */
    private static ?\WeakMap $instances = null;

    /**
     * @var Selectable<TKey,T>
     */
    private readonly Selectable $collection;

    private readonly Criteria $criteria;

    private readonly ?string $indexBy;

    /**
     * @var int<1,max>
     */
    private readonly int $itemsPerPage;

    /**
     * @param ReadableCollection<TKey,T>|Selectable<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     */
    final private function __construct(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        private readonly ?CountStrategy $count = null,
        private readonly ?Pagination $pagination = null,
    ) {
        $this->indexBy = $indexBy ?? Configuration::$defaultIndexBy;
        $this->itemsPerPage = $itemsPerPage ?? Configuration::$defaultItemsPerPage;

        // save collection

        if (!$collection instanceof Selectable) {
            throw new UnexpectedValueException('The wrapped collection must implement the Selectable interface.');
        }

        $this->collection = $collection;

        // save criteria

        $criteria = clone ($criteria ?? Criteria::create());

        if (\count($criteria->orderings()) === 0) {
            $criteria->orderBy(Configuration::$defaultOrderBy);
        }

        $this->criteria = $criteria;
    }

    /**
     * @template STKey of array-key
     * @template ST
     * @param ReadableCollection<STKey,ST>|Selectable<STKey,ST> $collection
     * @param int<1,max> $itemsPerPage
     */
    final public static function create(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        ?string $instanceId = null,
        ?string $indexBy = null,
        int $itemsPerPage = 50,
        ?CountStrategy $count = null,
        ?Pagination $pagination = null,
    ): static {
        if (self::$instances === null) {
            /** @var \WeakMap<object,array<string,self<array-key,mixed>>> */
            $weakmap = new \WeakMap();
            // @phpstan-ignore-next-line
            self::$instances = $weakmap;
        }

        $cacheKey = hash('xxh128', serialize([
            $instanceId ?? $criteria,
            $indexBy,
            $itemsPerPage,
            $pagination,
        ]));

        if (isset(self::$instances[$collection][$cacheKey])) {
            /** @var static */
            return self::$instances[$collection][$cacheKey];
        }

        /** @psalm-suppress UnsafeGenericInstantiation */
        $newInstance = new static(
            collection: $collection,
            criteria: $criteria,
            indexBy: $indexBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
            pagination: $pagination,
        );

        if (!isset(self::$instances[$collection])) {
            // @phpstan-ignore-next-line
            self::$instances[$collection] = [];
        }

        /**
         * @psalm-suppress InvalidArgument
         * @phpstan-ignore-next-line
         */
        self::$instances[$collection][$cacheKey] = $newInstance;

        /** @var static */
        return $newInstance;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    #[\Override]
    public function withItemsPerPage(int $itemsPerPage): static
    {
        return self::create(
            collection: $this->collection,
            criteria: $this->criteria,
            itemsPerPage: $itemsPerPage,
            count: $this->count,
        );
    }

    private function getUnderlyingCountable(): \Countable
    {
        return $this->collection->matching($this->criteria);
    }

    private function getCountStrategy(): CountStrategy
    {
        return $this->count ?? ParameterUtil::getDefaultCountStrategyForMinimalClasses();
    }
}
