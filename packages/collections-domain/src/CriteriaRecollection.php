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
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;
use Rekalogika\Contracts\Collections\ReadableRecollection;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Count\RestrictedCountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\ReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\SafeCollectionTrait;
use Rekalogika\Domain\Collections\Trait\CriteriaReadableTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionDxTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements ReadableRecollection<TKey,T>
 */
class CriteriaRecollection implements ReadableRecollection
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    /** @use SafeCollectionTrait<TKey,T> */
    use SafeCollectionTrait;

    /**
     * @use ReadableRecollectionTrait<TKey,T>
     * @use CriteriaReadableTrait<TKey,T>
     */
    use ReadableRecollectionTrait, CriteriaReadableTrait {
        CriteriaReadableTrait::contains insteadof ReadableRecollectionTrait;
        CriteriaReadableTrait::containsKey insteadof ReadableRecollectionTrait;
        CriteriaReadableTrait::get insteadof ReadableRecollectionTrait;
        CriteriaReadableTrait::slice insteadof ReadableRecollectionTrait;
    }

    /** @use RecollectionDxTrait<TKey,T> */
    use RecollectionDxTrait;

    /**
     * @var null|\WeakMap<object,array<string,self<array-key,mixed>>>
     */
    private static ?\WeakMap $instances = null;

    /**
     * @var Selectable<TKey,T>
     */
    private readonly Selectable $collection;

    private readonly Criteria $criteria;
    private readonly CountStrategy $count;

    /**
     * @param ReadableCollection<TKey,T>|Selectable<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     * @param null|int<1,max> $softLimit
     * @param null|int<1,max> $hardLimit
     */
    final private function __construct(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        private readonly ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        ?CountStrategy $count = null,
        private readonly ?int $softLimit = null,
        private readonly ?int $hardLimit = null,
    ) {
        // save collection

        if (!$collection instanceof Selectable) {
            throw new UnexpectedValueException('The wrapped collection must implement the Selectable interface.');
        }

        $this->collection = $collection;

        // save criteria

        $criteria = clone ($criteria ?? Criteria::create());

        if (\count($criteria->orderings()) === 0) {
            $criteria->orderBy(['id' => Order::Descending]);
        }

        $this->criteria = $criteria;

        // save count strategy

        $this->count = $count ?? new RestrictedCountStrategy();
    }

    /**
     * @template STKey of array-key
     * @template ST
     * @param ReadableCollection<STKey,ST>|Selectable<STKey,ST> $collection
     * @param int<1,max> $itemsPerPage
     * @param null|int<1,max> $softLimit
     * @param null|int<1,max> $hardLimit
     * @return static
     */
    final public static function create(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        ?string $instanceId = null,
        ?string $indexBy = null,
        int $itemsPerPage = 50,
        ?CountStrategy $count = null,
        ?int $softLimit = null,
        ?int $hardLimit = null,
    ): ReadableRecollection {
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
            softLimit: $softLimit,
            hardLimit: $hardLimit,
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

    private function getCountStrategy(): CountStrategy
    {
        return $this->count;
    }

    /**
     * @return ReadableCollection<TKey,T>
     */
    private function getRealCollection(): ReadableCollection
    {
        return $this->getSafeCollection();
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
     * @return non-empty-array<string,Order>
     */
    private function getOrderBy(): array
    {
        $ordering = $this->criteria->orderings();

        if (empty($ordering)) {
            return ['id' => Order::Descending];
        }

        return $ordering;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    final public function withItemsPerPage(int $itemsPerPage): static
    {
        return self::create(
            collection: $this->collection,
            criteria: $this->criteria,
            itemsPerPage: $itemsPerPage,
            count: $this->count,
            softLimit: $this->softLimit,
            hardLimit: $this->hardLimit,
        );
    }

    private function getUnderlyingCountable(): \Countable
    {
        return $this->collection->matching($this->criteria);
    }

    final protected function createCriteria(): Criteria
    {
        return clone $this->criteria;
    }
}
