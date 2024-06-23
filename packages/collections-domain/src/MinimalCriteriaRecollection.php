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
use Rekalogika\Domain\Collections\Common\Trait\MinimalReadableCollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
use Rekalogika\Domain\Collections\Common\Trait\ReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Trait\ExtraLazyDetectorTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements MinimalReadableRecollection<TKey,T>
 */
class MinimalCriteriaRecollection implements MinimalReadableRecollection, \Countable
{
    /** @use RecollectionTrait<TKey,T> */
    use RecollectionTrait;

    /** @use PageableTrait<TKey,T> */
    use PageableTrait;

    /** @use MinimalReadableCollectionTrait<TKey,T> */
    use MinimalReadableCollectionTrait;

    use CountableTrait;

    use ExtraLazyDetectorTrait;

    /** @use ReadableRecollectionTrait<TKey,T> */
    use ReadableRecollectionTrait;

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
    public function __construct(
        ReadableCollection $collection,
        ?Criteria $criteria = null,
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

    private function getCountStrategy(): CountStrategy
    {
        return $this->countStrategy;
    }

    private function &getProvidedCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param null|Collection<TKey,T> $collection
     * @param null|int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     */
    protected function with(
        ?ReadableCollection $collection = null,
        ?Criteria $criteria = null,
        ?int $itemsPerPage = 50,
        ?CountStrategy $countStrategy = null,
        ?int &$count = null,
    ): static {
        $count = $count ?? $this->count;

        // @phpstan-ignore-next-line
        return new static(
            collection: $collection ?? $this->collection,
            criteria: $criteria ?? $this->criteria,
            itemsPerPage: $itemsPerPage ?? $this->itemsPerPage,
            countStrategy: $countStrategy ?? $this->countStrategy,
            count: $count,
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
