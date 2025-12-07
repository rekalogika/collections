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
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Contracts\Collections\Exception\UnexpectedValueException;
use Rekalogika\Contracts\Collections\MinimalRecollection;
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Domain\Collections\Common\Trait\MinimalRecollectionTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements MinimalRecollection<TKey,T>
 * @api
 */
class MinimalRecollectionDecorator implements MinimalRecollection
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    /** @use MinimalRecollectionTrait<TKey,T> */
    use MinimalRecollectionTrait;

    /**
     * @var null|\WeakMap<object,array<string,self<array-key,mixed>>>
     */
    private static ?\WeakMap $instances = null;

    /**
     * @var Collection<TKey,T>&Selectable<TKey,T>
     */
    private readonly Collection&Selectable $collection;

    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;

    private readonly ?string $indexBy;

    private readonly Criteria $criteria;

    /**
     * @var int<1,max>
     */
    private readonly int $itemsPerPage;

    /**
     * @param Collection<TKey,T> $collection
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     */
    final private function __construct(
        Collection $collection,
        array|string|null $orderBy = null,
        ?string $indexBy = null,
        ?int $itemsPerPage = null,
        private readonly ?CountStrategy $count = null,
        private readonly ?KeyTransformer $keyTransformer = null,
        private readonly ?Pagination $pagination = null,
    ) {
        $this->indexBy = $indexBy ?? Configuration::$defaultIndexBy;
        $this->itemsPerPage = $itemsPerPage ?? Configuration::$defaultItemsPerPage;

        // handle collection

        if (!$collection instanceof Selectable) {
            throw new UnexpectedValueException('The wrapped collection must implement the Selectable interface.');
        }

        $this->collection = $collection;

        // handle orderBy

        $this->orderBy = ParameterUtil::normalizeOrderBy(
            orderBy: $orderBy,
            defaultOrderBy: $this->getDefaultOrderBy(),
        );

        $this->criteria = Criteria::create(true)->orderBy($this->orderBy);
    }

    /**
     * @template STKey of array-key
     * @template ST
     * @param Collection<STKey,ST> $collection
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     * @return static
     */
    final public static function create(
        Collection $collection,
        array|string|null $orderBy = null,
        ?string $indexBy = null,
        int $itemsPerPage = 50,
        ?CountStrategy $count = null,
        ?KeyTransformer $keyTransformer = null,
        ?Pagination $pagination = null,
    ): MinimalRecollection {
        if (self::$instances === null) {
            /** @var \WeakMap<object,array<string,self<array-key,mixed>>>    */
            $weakmap = new \WeakMap();
            // @phpstan-ignore-next-line
            self::$instances = $weakmap;
        }

        $cacheKey = hash('xxh128', serialize([
            $orderBy,
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
            orderBy: $orderBy,
            indexBy: $indexBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
            keyTransformer: $keyTransformer,
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

        /**
         * @var static
         * @phpstan-ignore varTag.nativeType
         */
        return $newInstance;
    }

    #[\Override]
    private function getCountStrategy(): CountStrategy
    {
        return $this->count ?? ParameterUtil::getDefaultCountStrategyForMinimalClasses();
    }

    /**
     * @return Collection<TKey,T>
     */
    #[\Override]
    private function getRealCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return non-empty-array<string,Order>
     */
    protected function getDefaultOrderBy(): array|string
    {
        return Configuration::$defaultOrderBy;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    #[\Override]
    final public function withItemsPerPage(int $itemsPerPage): static
    {
        return static::create(
            collection: $this->collection,
            orderBy: $this->orderBy,
            itemsPerPage: $itemsPerPage,
            count: $this->count,
            keyTransformer: $this->keyTransformer,
        );
    }

    #[\Override]
    private function getUnderlyingCountable(): \Countable
    {
        return $this->collection;
    }

    //
    // DX methods
    //

    /**
     * @return MinimalCriteriaRecollection<TKey,T>
     */
    final protected function createCriteriaRecollection(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
    ): MinimalCriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->orderBy);
        }

        return MinimalCriteriaRecollection::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );
    }

    /**
     * @return PageableRecollection<TKey,T>
     */
    final protected function createCriteriaPageable(
        Criteria $criteria,
        ?string $instanceId = null,
        ?CountStrategy $count = null,
    ): PageableRecollection {
        // if $criteria has no orderings, add the current ordering
        if ($criteria->orderings() === []) {
            $criteria = $criteria->orderBy($this->orderBy);
        }

        /**
         * @var PageableRecollection<TKey,T>
         * @phpstan-ignore-next-line
         */
        return CriteriaPageable::create(
            collection: $this->collection,
            criteria: $criteria,
            instanceId: $instanceId,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );
    }
}
