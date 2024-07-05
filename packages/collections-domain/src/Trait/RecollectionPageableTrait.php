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

namespace Rekalogika\Domain\Collections\Trait;

use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Exception\GettingCountUnsupportedException;
use Rekalogika\Rekapager\Doctrine\Collections\SelectableAdapter;
use Rekalogika\Rekapager\Keyset\KeysetPageable;

/**
 * @template TKey of array-key
 * @template T
 *
 * @internal
 */
trait RecollectionPageableTrait
{
    /**
     * @var int<0,max>
     */
    abstract private function getCount(): int;

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

        $adapter = new SelectableAdapter(
            collection: $this->collection,
            criteria: $this->criteria,
            indexBy: $this->indexBy,
        );

        $count = function (): int|bool {
            try {
                return $this->getCount();
            } catch (GettingCountUnsupportedException) {
                return false;
            }
        };

        $this->pageable = new KeysetPageable(
            adapter: $adapter,
            itemsPerPage: $this->itemsPerPage,
            count: $count,
        );

        return $this->pageable;
    }
}
