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
use PHPUnit\Framework\TestCase;
use Rekalogika\Domain\Collections\Common\Count\DelegatedCountStrategy;
use Rekalogika\Domain\Collections\Common\Count\PrecountingStrategy;
use Rekalogika\Domain\Collections\Common\Count\RestrictedCountStrategy;
use Rekalogika\Domain\Collections\Common\Exception\GettingCountUnsupportedException;
use Rekalogika\Domain\Collections\RecollectionDecorator;

class CountTest extends TestCase
{
    public function testDefaultCount(): void
    {
        $collection = RecollectionDecorator::create(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ])
        );

        $this->expectException(GettingCountUnsupportedException::class);
        $foo = \count($collection);
    }

    public function testRestrictedCount(): void
    {
        $collection = RecollectionDecorator::create(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            count: new RestrictedCountStrategy()
        );

        $this->expectException(GettingCountUnsupportedException::class);
        $foo = \count($collection);
    }

    public function testDelegatedCount(): void
    {
        $collection = RecollectionDecorator::create(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            count: new DelegatedCountStrategy()
        );

        $count = \count($collection);

        $this->assertEquals(3, $count);
    }

    public function testProvidedCount(): void
    {
        $count = 5;

        $collection = RecollectionDecorator::create(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            count: new PrecountingStrategy($count),
        );

        $result = \count($collection);
        $this->assertEquals(5, $result);

        $count = 10;

        $result = \count($collection);
        $this->assertEquals(10, $result);

        $collection->refreshCount();
        $result = \count($collection);
        $this->assertEquals(3, $result);
        $this->assertEquals(3, $count);
    }
}
