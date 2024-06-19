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

namespace Rekalogika\Collections\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Tests\Common\Collections\ArrayCollectionTestCase;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\RecollectionDecorator;

class CollectionTest extends ArrayCollectionTestCase
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<array-key,mixed>
     */
    protected function buildCollection(array $elements = []): Collection
    {
        return new RecollectionDecorator(
            collection: new ArrayCollection($elements),
            countStrategy: CountStrategy::Delegate,
        );
    }

    /** @psalm-return array<string, array{mixed[]}> */
    public static function provideDifferentElements(): array
    {
        return [
            'objects'     => [
                [
                    3 => new Citizen(3, 'Jack'),
                    2 => new Citizen(2, 'Jane'),
                    1 => new Citizen(1, 'John'),
                ]
            ],
        ];
    }
}
