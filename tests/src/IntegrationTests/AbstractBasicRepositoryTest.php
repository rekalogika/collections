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
use Rekalogika\Collections\Tests\App\BasicRepository\CountryBasicRepository;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Contracts\Collections\BasicRepository;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractBasicRepositoryTest extends KernelTestCase
{
    /**
     * @return BasicRepository<array-key,Country>
     */
    protected function getRepository(): BasicRepository
    {
        $repository = static::getContainer()->get(CountryBasicRepository::class);
        static::assertInstanceOf(BasicRepository::class, $repository);

        /** @var BasicRepository<array-key,Country> $repository */

        return $repository;
    }

    public function testGet(): void
    {
        $country = $this->getRepository()->get(1);
        static::assertInstanceOf(Country::class, $country);
    }

    public function testGetNegative(): void
    {
        $country = $this->getRepository()->get(9999999);
        static::assertNull($country);
    }

    public function testGetOrFail(): void
    {
        $country = $this->getRepository()->getOrFail(1);
        static::assertInstanceOf(Country::class, $country);
    }

    public function testGetOrFailNegative(): void
    {
        $this->expectException(NotFoundException::class);
        $country = $this->getRepository()->getOrFail(9999999);
    }

    public function testContains(): void
    {
        $country = $this->getRepository()->getOrFail(1);
        static::assertTrue($this->getRepository()->contains($country));
    }

    public function testContainsNegative(): void
    {
        $country = new Country();
        static::assertFalse($this->getRepository()->contains($country));
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
        $country = $this->getRepository()->reference(1);
        static::assertInstanceOf(Country::class, $country);
        $name = $country->getName();
        static::assertIsString($name);
    }

    public function testGetReferenceNegative(): void
    {
        $country = $this->getRepository()->reference(9999999);
        $this->expectException(EntityNotFoundException::class);
        $name = $country->getName();
    }

    public function testRemove(): void
    {
        $removed = $this->getRepository()->remove(1);
        static::assertFalse($this->getRepository()->contains($removed));
    }

    public function testRemoveNegative(): void
    {
        $removed = $this->getRepository()->remove(9999999);
        static::assertNull($removed);
    }

    public function testRemoveElement(): void
    {
        $country = $this->getRepository()->getOrFail(1);
        static::assertTrue($this->getRepository()->removeElement($country));
    }

    public function testRemoveElementNegative(): void
    {
        $country = new Country();
        static::assertFalse($this->getRepository()->removeElement($country));
    }

    public function testPageable(): void
    {
        $page = $this->getRepository()->getFirstPage();
        static::assertCount(3, $page);
    }
}
