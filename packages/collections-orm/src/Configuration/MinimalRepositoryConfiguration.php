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
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\OrderByUtil;

/**
 * @template T of object
 */
class MinimalRepositoryConfiguration
{
    /**
     * @var non-empty-array<string,Order>
     */
    private array $orderBy;

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
        private readonly CountStrategy $countStrategy = CountStrategy::Restrict,
    ) {
        $this->orderBy = OrderByUtil::normalizeOrderBy($orderBy);

        if ($countStrategy === CountStrategy::Provided) {
            throw new InvalidArgumentException('CountStrategy::Provided is not supported in repositories');
        }
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
        return $this->countStrategy;
    }
}
