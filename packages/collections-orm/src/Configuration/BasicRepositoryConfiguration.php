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
use Rekalogika\Domain\Collections\Common\CountStrategy;

class BasicRepositoryConfiguration
{
    /**
     * @var non-empty-array<string,Order>|string|null
     */
    private array|string|null $orderBy = null;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage = 50;

    private CountStrategy $countStrategy = CountStrategy::Restrict;

    /**
     * @return non-empty-array<string,Order>|string|null
     */
    public function getOrderBy(): null|array|string
    {
        return $this->orderBy;
    }

    /**
     * @param non-empty-array<string,Order>|string $orderBy
     */
    public function setOrderBy($orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return int<1,max>
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function getCountStrategy(): CountStrategy
    {
        return $this->countStrategy;
    }

    public function setCountStrategy(CountStrategy $countStrategy): self
    {
        $this->countStrategy = $countStrategy;

        return $this;
    }
}
