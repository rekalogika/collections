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
use Rekalogika\Contracts\Collections\Recollection;

/**
 * @template-covariant R of Recollection<array-key,Citizen>
 */
trait ArrayAccessTestsTrait
{
    public function testOffsetExists(): void
    {
        $citizen = $this->getOne();
        static::assertTrue($this->getObject()->offsetExists($citizen->getId() ?? -1));
    }

    public function testOffsetExistsNegative(): void
    {
        static::assertFalse($this->getObject()->containsKey(999999));
    }

    public function testOffsetGet(): void
    {
        $citizen = $this->getOne();
        $key = $citizen->getId();
        static::assertNotNull($key);

        $citizen2 = $this->getObject()->offsetGet($key);
        static::assertInstanceOf(Citizen::class, $citizen2);
        static::assertSame($citizen, $citizen2);
    }

    public function testOffsetSetWithIndex(): void
    {
        $this->testSafety();
        $citizen = new Citizen();
        $this->getObject()->offsetSet(99999, $citizen);
        static::assertSame($citizen, $this->getObject()->offsetGet(99999));
    }

    public function testOffsetSetWithoutIndex(): void
    {
        $citizen = new Citizen();
        $this->getObject()->offsetSet(null, $citizen);
        $this->getObject()->contains($citizen);
    }

    public function testOffsetUnset(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->first();
        static::assertInstanceOf(Citizen::class, $citizen);
        $key = $citizen->getId();
        static::assertNotNull($key);
        $this->getObject()->offsetUnset($key);
        static::assertNull($this->getObject()->offsetGet($key));
    }
}
