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
use Rekalogika\Contracts\Collections\MinimalRecollection;
use Rekalogika\Contracts\Collections\Recollection;

/**
 * @template-covariant R of MinimalRecollection<array-key,Citizen>|Recollection<array-key,Citizen>
 */
trait MinimalRecollectionTestsTrait
{
    /** @use MinimalReadableRecollectionTestsTrait<R> */
    use MinimalReadableRecollectionTestsTrait;

    public function testAddAndTestByContains(): void
    {
        $citizen = new Citizen();
        $citizen->setName('John Doe');
        $citizen->setAge(30);

        static::assertFalse($this->getObject()->contains($citizen));
        $this->getObject()->add($citizen);
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testAddAndTestByIteration(): void
    {
        $object = $this->getObject();

        // @phpstan-ignore function.alreadyNarrowedType
        if (!is_iterable($object)) {
            static::markTestSkipped('This test only works with iterable');
        }

        if (!$this->isSafe()) {
            $this->expectException(\OverflowException::class);
        }

        $citizen = new Citizen();
        $citizen->setName('John Doe');
        $citizen->setAge(30);

        static::assertNotContains($citizen, $object);
        $this->getObject()->add($citizen);
        static::assertContains($citizen, $object);
    }
}
