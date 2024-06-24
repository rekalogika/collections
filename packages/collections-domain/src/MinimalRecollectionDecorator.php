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
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\OrderByUtil;
use Rekalogika\Domain\Collections\Common\Trait\CountableTrait;
use Rekalogika\Domain\Collections\Common\Trait\MinimalRecollectionTrait;
use Rekalogika\Domain\Collections\Trait\RecollectionPageableTrait;

/**
 * @template TKey of array-key
 * @template T
 * @implements MinimalRecollection<TKey,T>
 */
class MinimalRecollectionDecorator implements MinimalRecollection, \Countable
{
    /** @use RecollectionPageableTrait<TKey,T> */
    use RecollectionPageableTrait;

    /** @use MinimalRecollectionTrait<TKey,T> */
    use MinimalRecollectionTrait;

    use CountableTrait;

    /**
     * @var Collection<TKey,T>&Selectable<TKey,T>
     */
    private readonly Collection&Selectable $collection;

    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;

    private readonly Criteria $criteria;

    /**
     * @param Collection<TKey,T> $collection
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     */
    public function __construct(
        Collection $collection,
        array|string|null $orderBy = null,
        private readonly ?string $indexBy = null,
        private readonly int $itemsPerPage = 50,
        private readonly CountStrategy $countStrategy = CountStrategy::Restrict,
        private ?int &$count = null,
    ) {
        // handle collection

        if (!$collection instanceof Selectable) {
            throw new UnexpectedValueException('The wrapped collection must implement the Selectable interface.');
        }

        $this->collection = $collection;

        // handle orderBy

        $this->orderBy = OrderByUtil::normalizeOrderBy(
            orderBy: $orderBy,
            defaultOrderBy: $this->getDefaultOrderBy()
        );

        $this->criteria = Criteria::create()->orderBy($this->orderBy);
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
     * @return Collection<TKey,T>
     */
    private function getRealCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return non-empty-array<string,Order>|string
     */
    protected function getDefaultOrderBy(): array|string
    {
        return Configuration::$defaultOrderBy;
    }

    /**
     * @param null|Collection<TKey,T> $collection
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param null|int<1,max> $itemsPerPage
     * @param null|int<0,max> $count
     */
    protected function with(
        ?Collection $collection = null,
        array|string|null $orderBy = null,
        ?int $itemsPerPage = 50,
        ?CountStrategy $countStrategy = CountStrategy::Restrict,
        ?int &$count = null,
    ): static {
        $count = $count ?? $this->count;

        // @phpstan-ignore-next-line
        return new static(
            collection: $collection ?? $this->collection,
            orderBy: $orderBy ?? $this->orderBy,
            itemsPerPage: $itemsPerPage ?? $this->itemsPerPage,
            countStrategy: $countStrategy ?? $this->countStrategy,
            count: $count,
        );
    }

    /**
     * @param null|int<0,max> $count
     * @return MinimalCriteriaRecollection<TKey,T>
     */
    protected function applyCriteria(
        Criteria $criteria,
        CountStrategy $countStrategy = CountStrategy::Restrict,
        ?int &$count = null,
    ): MinimalCriteriaRecollection {
        // if $criteria has no orderings, add the current ordering
        if (\count($criteria->orderings()) === 0) {
            $criteria = $criteria->orderBy($this->orderBy);
        }

        return new MinimalCriteriaRecollection(
            collection: $this->collection,
            criteria: $criteria,
            itemsPerPage: $this->itemsPerPage,
            countStrategy: $countStrategy,
            count: $count,
        );
    }

    /**
     * @return int<0,max>
     */
    private function getRealCount(): int
    {
        $count = $this->collection->count();

        if ($count > 0) {
            return $count;
        }

        return 0;
    }
}
