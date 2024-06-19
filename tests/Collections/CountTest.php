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
use PHPUnit\Framework\TestCase;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Exception\CountDisabledException;
use Rekalogika\Domain\Collections\RecollectionDecorator;

class CountTest extends TestCase
{
    public function testDefaultCount(): void
    {
        $collection = new RecollectionDecorator(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ])
        );

        $this->expectException(CountDisabledException::class);
        $foo = \count($collection);
    }

    public function testRestrictedCount(): void
    {
        $collection = new RecollectionDecorator(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            countStrategy: CountStrategy::Restrict
        );

        $this->expectException(CountDisabledException::class);
        $foo = \count($collection);
    }

    public function testDelegatedCount(): void
    {
        $collection = new RecollectionDecorator(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            countStrategy: CountStrategy::Delegate
        );

        $count = \count($collection);

        $this->assertEquals(3, $count);
    }

    public function testProvidedCount(): void
    {
        $count = 5;

        $collection = new RecollectionDecorator(
            collection: new ArrayCollection([
                new Citizen(3, 'John Doe'),
                new Citizen(2, 'Jane Doe'),
                new Citizen(1, 'John Smith'),
            ]),
            countStrategy: CountStrategy::Provided,
            count: $count
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
