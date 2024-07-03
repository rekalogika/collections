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
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
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

    use CountableTrait;

    /**
     * @var null|\WeakMap<object,array<string,self<array-key,mixed>>>
     */
    private static ?\WeakMap $instances = null;

    /**
     * @var Selectable<TKey,T>
     */
    private readonly Selectable $collection;

    private readonly Criteria $criteria;

    /**
     * @param ReadableCollection<TKey,T>|Selectable<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     */
    final private function __construct(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        private readonly ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        private readonly ?CountStrategy $count = null,
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
    }

    /**
     * @template STKey of array-key
     * @template ST
     * @param ReadableCollection<STKey,ST>|Selectable<STKey,ST> $collection
     * @param int<1,max> $itemsPerPage
     * @return static
     */
    final public static function create(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        ?string $instanceId = null,
        ?string $indexBy = null,
        int $itemsPerPage = 50,
        ?CountStrategy $count = null,
    ): PageableInterface {
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

    private function getCountStrategy(): ?CountStrategy
    {
        return $this->count;
    }
}
