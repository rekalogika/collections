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

use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Domain\Collections\Internal\DirectClosureExpressionVisitor;

/**
 * @template TKey of array-key
 * @template T
 * @extends DoctrineArrayCollection<TKey,T>
 */
final class ArrayCollection extends DoctrineArrayCollection
{
    /**
     * @param array<TKey,T> $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * @psalm-return Collection<TKey,T>&Selectable<TKey,T>
     */
    #[\Override]
    public function matching(Criteria $criteria): Collection&Selectable
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->toArray();

        if ($expr) {
            $visitor  = new DirectClosureExpressionVisitor();
            /** @var \Closure(T):bool */
            $filter   = $visitor->dispatch($expr);
            $filtered = array_filter($filtered, $filter);
        }

        $orderings = $criteria->orderings();

        if ($orderings !== []) {
            $next = null;
            foreach (array_reverse($orderings) as $field => $ordering) {
                /** @var \Closure(mixed,mixed):int */
                $next = DirectClosureExpressionVisitor::sortByField($field, $ordering === Order::Descending ? -1 : 1, $next);
            }

            uasort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset !== null && $offset > 0 || $length !== null && $length > 0) {
            $filtered = \array_slice($filtered, (int) $offset, $length, true);
        }

        return $this->createFrom($filtered);
    }
}
