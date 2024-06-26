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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\MinimalReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements MinimalReadableRecollection<TKey,T>
 */
class MinimalCriteriaRecollection implements MinimalReadableRecollection, \Countable
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    use CountableTrait;

    /** @use MinimalReadableRecollectionTrait<TKey,T> */
    use MinimalReadableRecollectionTrait;

    /**
     * @var null|\WeakMap<object,array<string,self<array-key,mixed>>>
     */
    private static ?\WeakMap $instances = null;

    /**
     * @var ReadableCollection<TKey,T>&Selectable<TKey,T>
     */
    private readonly ReadableCollection&Selectable $collection;

    private readonly Criteria $criteria;

    /**
     * @param ReadableCollection<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     */
    final private function __construct(
        ReadableCollection $collection,
        ?Criteria $criteria = null,
        private readonly ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        private readonly CountStrategy $countStrategy = CountStrategy::Restrict,
        private ?int &$count = null,
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
     * @param Collection<STKey,ST> $collection
     * @param int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     * @return static
     */
    final public static function create(
        Collection $collection,
        ?Criteria $criteria = null,
        ?string $instanceId = null,
        ?string $indexBy = null,
        int $itemsPerPage = 50,
        CountStrategy $countStrategy = CountStrategy::Restrict,
        ?int &$count = null,
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
            $countStrategy,
            $count,
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
            countStrategy: $countStrategy,
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

    private function getCountStrategy(): CountStrategy
    {
        return $this->countStrategy;
    }

    private function &getProvidedCount(): ?int
    {
        return $this->count;
    }

    /**
     * @return ReadableCollection<TKey,T>
     */
    private function getRealCollection(): ReadableCollection
    {
        return $this->collection;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function withItemsPerPage(int $itemsPerPage): static
    {
        /** @psalm-suppress UnsafeGenericInstantiation */
        return new static(
            collection: $this->collection,
            criteria: $this->criteria,
            itemsPerPage: $itemsPerPage,
            countStrategy: $this->countStrategy,
            count: $this->count,
        );
    }

    /**
     * @return int<0,max>
     */
    private function getRealCount(): int
    {
        $count = $this->collection->matching($this->criteria)->count();

        if ($count > 0) {
            return $count;
        }

        return 0;
    }
}
