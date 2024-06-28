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

namespace Rekalogika\Collections\ORM\Configuration;

use Doctrine\Common\Collections\Order;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Count\RestrictedCountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\OrderByUtil;

/**
 * @template T of object
 */
class MinimalRepositoryConfiguration
{
    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;
    private readonly CountStrategy $count;

    /**
     * @param class-string<T> $class
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     */
    public function __construct(
        private readonly string $class,
        private readonly string $indexBy = 'id',
        array|string|null $orderBy = null,
        private readonly int $itemsPerPage = 50,
        ?CountStrategy $count = null,
    ) {
        $this->orderBy = OrderByUtil::normalizeOrderBy($orderBy);

        $this->count = $count ?? new RestrictedCountStrategy();
    }

    public function getIndexBy(): string
    {
        return $this->indexBy;
    }

    /**
     * @return class-string<T>
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return non-empty-array<string,Order>
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return int<1,max>
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getCountStrategy(): CountStrategy
    {
        return $this->count;
    }
}
