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
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;
use Rekalogika\Domain\Collections\Common\Trait\MinimalReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\SafeCollectionTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements MinimalReadableRecollection<TKey,T>
 */
class MinimalCriteriaRecollection implements MinimalReadableRecollection
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    /** @use MinimalReadableRecollectionTrait<TKey,T> */
    use MinimalReadableRecollectionTrait;

    /** @use SafeCollectionTrait<TKey,T> */
    use SafeCollectionTrait;

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
     * @param ReadableCollection<TKey,T>|Selectable<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     */
    final private function __construct(
        ReadableCollection|Selectable $collection,
        ?Criteria $criteria = null,
        ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        private readonly ?CountStrategy $count = null,
        private readonly ?KeyTransformer $keyTransformer = null,
    ) {
        $this->indexBy = $indexBy ?? Configuration::$defaultIndexBy;

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
        ?KeyTransformer $keyTransformer = null,
    ): MinimalReadableRecollection {
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
            keyTransformer: $keyTransformer,
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

    private function getCountStrategy(): ?CountStrategy
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
     * @param int<1,max> $itemsPerPage
     */
    final public function withItemsPerPage(int $itemsPerPage): static
    {
        return self::create(
            collection: $this->collection,
            criteria: $this->criteria,
            itemsPerPage: $itemsPerPage,
            count: $this->count,
            keyTransformer: $this->keyTransformer,
        );
    }

    private function getUnderlyingCountable(): \Countable
    {
        return $this->collection->matching($this->criteria);
    }
}
