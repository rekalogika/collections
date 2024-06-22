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
final readonly class BasicRepositoryConfiguration
{
    /**
     * @var non-empty-array<string,Order>
     */
    private array $orderBy;

    /**
     * @param class-string<T> $class
     * @param non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     */
    public function __construct(
        private string $class,
        private string $identifier = 'id',
        array|string $orderBy = 'id',
        private int $itemsPerPage = 50,
        private CountStrategy $countStrategy = CountStrategy::Restrict,
    ) {
        $this->orderBy = OrderByUtil::normalizeOrderBy($orderBy);

        if ($countStrategy === CountStrategy::Provided) {
            throw new InvalidArgumentException('CountStrategy::Provided is not supported in repositories');
        }
    }

    /**
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param null|int<1,max> $itemsPerPage
     */
    public function with(
        ?string $indexBy = null,
        array|string|null $orderBy = null,
        ?int $itemsPerPage = null,
        ?CountStrategy $countStrategy = null,
    ): static {
        return new static(
            $this->class,
            $indexBy ?? $this->identifier,
            $orderBy ?? $this->orderBy,
            $itemsPerPage ?? $this->itemsPerPage,
            $countStrategy ?? $this->countStrategy,
        );
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
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
