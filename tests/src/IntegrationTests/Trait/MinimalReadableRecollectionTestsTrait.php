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

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;

/**
 * @template-covariant R of MinimalReadableRecollection<array-key,Citizen>|ReadableCollection<array-key,Citizen>
 */
trait MinimalReadableRecollectionTestsTrait
{
    /** @use PageableTestsTrait<R> */
    use PageableTestsTrait;

    public function testContains(): void
    {
        $citizen = $this->getOne();
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testContainsNegative(): void
    {
        $citizen = new Citizen();
        static::assertFalse($this->getObject()->contains($citizen));
    }

    public function testContainsKey(): void
    {
        $citizen = $this->getOne();
        static::assertTrue($this->getObject()->containsKey($citizen->getId() ?? -1));
    }

    public function testContainsKeyNegative(): void
    {
        static::assertFalse($this->getObject()->containsKey(9999999));
    }

    public function testGet(): void
    {
        $citizen = $this->getOne();
        $citizen2 = $this->getObject()->get($citizen->getId() ?? -1);
        static::assertSame($citizen, $citizen2);
    }

    public function testGetNegative(): void
    {
        $citizen = $this->getObject()->get(9999999);
        static::assertNull($citizen);
    }

    public function testGetOrFail(): void
    {
        $citizen = $this->getOne();
        $citizen2 = $this->getObject()->getOrFail($citizen->getId() ?? -1);
        static::assertSame($citizen, $citizen2);
    }

    public function testGetOrFailNegative(): void
    {
        $this->expectException(NotFoundException::class);
        $citizen = $this->getObject()->getOrFail(9999999);
    }
}
