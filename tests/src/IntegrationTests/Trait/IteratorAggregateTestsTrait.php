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

namespace Rekalogika\Collections\Tests\IntegrationTests\Trait;

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\ReadableRecollection;

/**
 * @template-covariant R of ReadableRecollection<array-key,Citizen>
 */
trait IteratorAggregateTestsTrait
{
    public function testGetIterator(): void
    {
        $iterator = $this->getObject()->getIterator();
        static::assertInstanceOf(\Traversable::class, $iterator);
    }

    public function testIteratorIteration(): void
    {
        $this->testSafety();
        $iterator = $this->getObject()->getIterator();
        $count = 0;
        foreach ($iterator as $key => $value) {
            static::assertIsInt($key);
            static::assertInstanceOf(Citizen::class, $value);
            static::assertEquals($key, $value->getId());
            $count++;
        }

        static::assertGreaterThan(0, $count);
    }
}
