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

use Rekalogika\Domain\Collections\Common\Trait\FindFetchTrait;
use Rekalogika\Domain\Collections\Common\Trait\ReadableRecollectionTrait;
use Rekalogika\Domain\Collections\Common\Trait\RefreshableCountTrait;

/**
 * @template TKey of array-key
 * @template-covariant T of object
 *
 * @internal
 */
trait ReadableRepositoryTrait
{
    /**
     * @use ReadableRecollectionTrait<TKey,T>
     * @use MinimalReadableRepositoryTrait<TKey,T>
     */
    use MinimalReadableRepositoryTrait, ReadableRecollectionTrait {
        MinimalReadableRepositoryTrait::reference insteadof ReadableRecollectionTrait;
        MinimalReadableRepositoryTrait::contains insteadof ReadableRecollectionTrait;
        MinimalReadableRepositoryTrait::containsKey insteadof ReadableRecollectionTrait;
        MinimalReadableRepositoryTrait::get insteadof ReadableRecollectionTrait;
    }

    /**
     * @use FindFetchTrait<TKey,T>
     */
    use FindFetchTrait;

    use RefreshableCountTrait;

    /**
     * @return array<TKey,T>
     */
    public function slice(int $offset, int|null $length = null): array
    {
        $queryBuilder = (clone $this->queryBuilder)->setFirstResult($offset);

        if ($length !== null) {
            $queryBuilder->setMaxResults($length);
        }

        /** @var array<TKey,T> */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
