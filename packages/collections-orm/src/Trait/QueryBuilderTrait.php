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
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Rekapager\Doctrine\ORM\QueryBuilderAdapter;
use Rekalogika\Rekapager\Keyset\KeysetPageable;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait QueryBuilderTrait
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
        );

        $count = match ($this->countStrategy) {
            CountStrategy::Restrict => false,
            CountStrategy::Delegate => true,
            CountStrategy::Provided => $this->count,
        }
            ?? 0;

        // @phpstan-ignore-next-line
        $this->pageable = new KeysetPageable(
            adapter: $adapter,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );

        // @phpstan-ignore-next-line
        return $this->pageable;
    }

    /**
     * @return int<0,max>
     */
    private function getRealCount(): int
    {
        $pagination = new Paginator($this->queryBuilder->getQuery());

        $count = $pagination->count();

        if ($count > 0) {
            return $count;
        }

        return 0;
    }
}
