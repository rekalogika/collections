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
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\Repository;

/**
 * @template R of Repository<array-key,Citizen>
 */
trait RepositoryTestsTrait
{
    /** @use RecollectionTestsTrait<R> */
    use RecollectionTestsTrait;

    /** @use ReadableRepositoryTestsTrait<R> */
    use ReadableRepositoryTestsTrait;

    public function testSet(): void
    {
        $citizen = new Citizen();
        $citizen->setName('John Doe');
        $citizen->setAge(30);

        $this->getObject()->set(99999, $citizen);
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testSetNull(): void
    {
        $citizen = new Citizen();
        $this->getObject()->set(null, $citizen);
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testOffsetSetWithIndex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $citizen = new Citizen();
        $this->getObject()->offsetSet(99999, $citizen);
    }
}
