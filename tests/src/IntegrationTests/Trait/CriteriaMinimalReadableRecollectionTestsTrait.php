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
trait CriteriaMinimalReadableRecollectionTestsTrait
{
    /** @use PageableTestsTrait<R> */
    use PageableTestsTrait;

    public function testContains(): void
    {
        $this->testSafety();
        $citizen = $this->getOne();
        static::assertTrue($this->getObject()->contains($citizen));
    }

    public function testContainsNegative(): void
    {
        $this->testSafety();
        $citizen = new Citizen();
        static::assertFalse($this->getObject()->contains($citizen));
    }

    public function testContainsKey(): void
    {
        $this->testSafety();
        $citizen = $this->getOne();
        static::assertTrue($this->getObject()->containsKey($citizen->getId() ?? -1));
    }

    public function testContainsKeyNull(): void
    {
        static::assertFalse($this->getObject()->containsKey(null));
    }

    public function testContainsKeyNegative(): void
    {
        $this->testSafety();
        static::assertFalse($this->getObject()->containsKey(9999999));
    }

    public function testGet(): void
    {
        $this->testSafety();
        $citizen = $this->getOne();
        $citizen2 = $this->getObject()->get($citizen->getId() ?? -1);
        static::assertSame($citizen, $citizen2);
    }

    public function testGetNull(): void
    {
        $citizen = $this->getObject()->get(null);
        static::assertNull($citizen);
    }

    public function testGetNegative(): void
    {
        $this->testSafety();
        $citizen = $this->getObject()->get(9999999);
        static::assertNull($citizen);
    }

    public function testFetch(): void
    {
        $this->testSafety();
        $citizen = $this->getOne();
        $citizen2 = $this->getObject()->fetch($citizen->getId() ?? -1);
        static::assertSame($citizen, $citizen2);
    }

    public function testFetchNull(): void
    {
        $this->expectException(NotFoundException::class);
        $citizen = $this->getObject()->fetch(null);
    }

    public function testFetchNegative(): void
    {
        $this->testSafety(NotFoundException::class);
        $citizen = $this->getObject()->fetch(9999999);
    }

    public function testSlice(): void
    {
        $this->testSafety();
        $slice = $this->getObject()->slice(0, 3);
        static::assertIsArray($slice);
        static::assertCount(3, $slice);
    }
}
