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
use Rekalogika\Domain\Collections\Common\KeyTransformer\KeyTransformer;

/**
 * @template T of object
 * @extends MinimalRepositoryConfiguration<T>
 */
class RepositoryConfiguration extends MinimalRepositoryConfiguration
{
    /**
     * @param class-string<T> $class
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     * @param null|int<1,max> $softLimit
     * @param null|int<1,max> $hardLimit
     */
    public function __construct(
        string $class,
        string $indexBy = 'id',
        array|string|null $orderBy = null,
        int $itemsPerPage = 50,
        ?CountStrategy $count = null,
        private readonly ?int $softLimit = null,
        private readonly ?int $hardLimit = null,
        private readonly ?KeyTransformer $keyTransformer = null,
    ) {
        parent::__construct(
            class: $class,
            indexBy: $indexBy,
            orderBy: $orderBy,
            itemsPerPage: $itemsPerPage,
            count: $count,
        );
    }

    /**
     * @return null|int<1,max>
     */
    public function getSoftLimit(): ?int
    {
        return $this->softLimit;
    }

    /**
     * @return null|int<1,max>
     */
    public function getHardLimit(): ?int
    {
        return $this->hardLimit;
    }

    /**
     * @return null|KeyTransformer
     */
    public function getKeyTransformer(): ?KeyTransformer
    {
        return $this->keyTransformer;
    }
}
