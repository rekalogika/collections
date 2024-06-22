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

use Doctrine\ORM\EntityNotFoundException;
use Rekalogika\Collections\Tests\App\BasicRepository\CitizenBasicRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\BasicRepository;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractBasicRepositoryTest extends KernelTestCase
{
    /**
     * @return BasicRepository<array-key,Citizen>
     */
    protected function getRepository(): BasicRepository
    {
        $repository = static::getContainer()->get(CitizenBasicRepository::class);
        static::assertInstanceOf(BasicRepository::class, $repository);

        /** @var BasicRepository<array-key,Citizen> $repository */

        return $repository;
    }

    public function testGet(): void
    {
        $citizen = $this->getRepository()->get(1);
        static::assertInstanceOf(Citizen::class, $citizen);
    }

    public function testGetNegative(): void
    {
        $citizen = $this->getRepository()->get(9999999);
        static::assertNull($citizen);
    }

    public function testGetOrFail(): void
    {
        $citizen = $this->getRepository()->getOrFail(1);
        static::assertInstanceOf(Citizen::class, $citizen);
    }

    public function testGetOrFailNegative(): void
    {
        $this->expectException(NotFoundException::class);
        $citizen = $this->getRepository()->getOrFail(9999999);
    }

    public function testContains(): void
    {
        $citizen = $this->getRepository()->getOrFail(1);
        static::assertTrue($this->getRepository()->contains($citizen));
    }

    public function testContainsNegative(): void
    {
        $citizen = new Citizen();
        static::assertFalse($this->getRepository()->contains($citizen));
    }

    public function testContainsKey(): void
    {
        static::assertTrue($this->getRepository()->containsKey(1));
    }

    public function testContainsKeyNegative(): void
    {
        static::assertFalse($this->getRepository()->containsKey(9999999));
    }

    public function testGetReference(): void
    {
        $citizen = $this->getRepository()->getReference(1);
        static::assertInstanceOf(Citizen::class, $citizen);
        $name = $citizen->getName();
        static::assertIsString($name);
    }

    public function testGetReferenceNegative(): void
    {
        $citizen = $this->getRepository()->getReference(9999999);
        $this->expectException(EntityNotFoundException::class);
        $name = $citizen->getName();
    }

    public function testRemove(): void
    {
        $citizen = $this->getRepository()->remove(1);
        static::assertFalse($this->getRepository()->contains($citizen));
    }

    public function testRemoveNegative(): void
    {
        $removed = $this->getRepository()->remove(9999999);
        static::assertNull($removed);
    }

    public function testRemoveElement(): void
    {
        $citizen = $this->getRepository()->getOrFail(1);
        static::assertTrue($this->getRepository()->removeElement($citizen));
    }

    public function testRemoveElementNegative(): void
    {
        $citizen = new Citizen();
        static::assertFalse($this->getRepository()->removeElement($citizen));
    }

    public function testPageable(): void
    {
        $page = $this->getRepository()->getFirstPage();
        static::assertCount(50, $page);

        $items = iterator_to_array($page);

        foreach ($items as $key => $item) {
            static::assertInstanceOf(Citizen::class, $item);
            static::assertEquals($key, $item->getId());
        }

        $page = $page->getNextPage();
        static::assertNotNull($page);

    }
}
