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
use Rekalogika\Contracts\Collections\ReadableRecollection;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Trait\ReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\SafeCollectionTrait;
use Rekalogika\Domain\Collections\Trait\ReadableExtraLazyTrait;
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
     * @use ReadableExtraLazyTrait<TKey,T>
     */
    use ReadableRecollectionTrait, ReadableExtraLazyTrait {
        ReadableExtraLazyTrait::contains insteadof ReadableRecollectionTrait;
        ReadableExtraLazyTrait::containsKey insteadof ReadableRecollectionTrait;
        ReadableExtraLazyTrait::get insteadof ReadableRecollectionTrait;
        ReadableExtraLazyTrait::slice insteadof ReadableRecollectionTrait;
    }

    /**
     * @var ReadableCollection<TKey,T>&Selectable<TKey,T>
     */
    private readonly ReadableCollection&Selectable $collection;

    private readonly Criteria $criteria;

    /**
     * @param ReadableCollection<TKey,T> $collection
     * @param int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     * @param null|int<1,max> $softLimit
     * @param null|int<1,max> $hardLimit
     */
    final public function __construct(
        ReadableCollection $collection,
        ?Criteria $criteria = null,
        private readonly ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        private readonly CountStrategy $countStrategy = CountStrategy::Restrict,
        private ?int &$count = null,
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
            softLimit: $this->softLimit,
            hardLimit: $this->hardLimit,
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
