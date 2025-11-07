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

use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;
use Rekalogika\Collections\Tests\UnitTests\Collections\Fixtures\Citizen;
use Rekalogika\Collections\Tests\UnitTests\Collections\Fixtures\Country;
use Rekalogika\Collections\Tests\UnitTests\Collections\Fixtures\NullCountry;
use Rekalogika\Domain\Collections\ArrayCollection;

final class ArrayCollectionTest extends TestCase
{
    /**
     * @return array<int,Citizen>
     */
    private function createArray(): array
    {
        $country = new Country('Khemed');

        return [
            new Citizen(3, 'John Doe', $country),
            new Citizen(2, 'Jane Doe', $country),
            new Citizen(1, 'John Smith'),
        ];
    }

    public function testDoctrineArrayCollection(): void
    {
        $citizens = new DoctrineArrayCollection($this->createArray());
        $statelessCriteria = Criteria::create()
            ->where(Criteria::expr()->isNull('country'));

        $statelessCitizens = $citizens->matching($statelessCriteria);
        static::assertCount(0, $statelessCitizens); // wrong count
    }

    public function testOurArrayCollection(): void
    {
        /** @psalm-suppress DeprecatedClass */
        $citizens = new ArrayCollection($this->createArray());
        $statelessCriteria = Criteria::create()
            ->where(Criteria::expr()->isNull('country'));

        $statelessCitizens = $citizens->matching($statelessCriteria);
        static::assertCount(1, $statelessCitizens);
    }

    public function testParentPrivateProperty(): void
    {
        /** @psalm-suppress DeprecatedClass */
        $citizens = new ArrayCollection([
            new Country('Khemed'),
            new Country('San Theodoros'),
            new Country('Borduria'),
            new Country('Syldavia'),
            new NullCountry(),
        ]);
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('foo', 'bar'));

        $result = $citizens->matching($criteria);
        static::assertCount(5, $result);
    }
}
