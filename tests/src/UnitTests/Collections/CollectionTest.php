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

namespace Rekalogika\Collections\Tests\UnitTests\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Tests\Common\Collections\ArrayCollectionTestCase;
use Rekalogika\Collections\Tests\UnitTests\Collections\Fixtures\Citizen;
use Rekalogika\Domain\Collections\Common\Count\DelegatedCountStrategy;
use Rekalogika\Domain\Collections\RecollectionDecorator;

class CollectionTest extends ArrayCollectionTestCase
{
    /**
     * @param mixed[] $elements
     *
     * @return Collection<array-key,mixed>
     */
    #[\Override]
    protected function buildCollection(array $elements = []): Collection
    {
        return RecollectionDecorator::create(
            collection: new ArrayCollection($elements),
            count: new DelegatedCountStrategy(),
        );
    }

    /** @psalm-return array<string, array{mixed[]}> */
    #[\Override]
    public static function provideDifferentElements(): array
    {
        return [
            'objects'     => [
                [
                    3 => new Citizen(3, 'Jack'),
                    2 => new Citizen(2, 'Jane'),
                    1 => new Citizen(1, 'John'),
                ],
            ],
        ];
    }
}
