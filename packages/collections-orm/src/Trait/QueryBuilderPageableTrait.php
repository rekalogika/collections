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

namespace Rekalogika\Collections\ORM\Trait;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Exception\GettingCountUnsupportedException;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Rekapager\Doctrine\ORM\QueryBuilderAdapter;
use Rekalogika\Rekapager\Keyset\KeysetPageable;
use Rekalogika\Rekapager\Offset\OffsetPageable;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait QueryBuilderPageableTrait
{
    /**
     * @var null|PageableInterface<TKey,T>
     */
    private ?PageableInterface $pageable = null;

    /**
     * @return PageableInterface<TKey,T>
     */
    private function getPageable(): PageableInterface
    {
        if ($this->pageable !== null) {
            return $this->pageable;
        }

        $adapter = new QueryBuilderAdapter(
            queryBuilder: $this->queryBuilder,
            indexBy: $this->indexBy,
            seekMethod: $this->seekMethod,
            lockMode: $this->lockMode,
        );

        $count = function (): int|bool {
            try {
                return $this->getCount();
            } catch (GettingCountUnsupportedException) {
                return false;
            }
        };

        // @phpstan-ignore-next-line
        $this->pageable = match ($this->pagination ?? Configuration::$defaultPagination) {
            Pagination::Keyset => new KeysetPageable(
                adapter: $adapter,
                itemsPerPage: $this->itemsPerPage,
                count: $count,
            ),
            Pagination::Offset => new OffsetPageable(
                adapter: $adapter,
                itemsPerPage: $this->itemsPerPage,
                count: $count,
            ),
        };

        // @phpstan-ignore-next-line
        return $this->pageable;
    }

    private function getUnderlyingCountable(): \Countable
    {
        return new Paginator($this->queryBuilder->getQuery());
    }
}
