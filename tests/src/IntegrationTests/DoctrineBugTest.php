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

namespace Rekalogika\Collections\Tests\IntegrationTests;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\App\MinimalRepository\CountryMinimalRepository;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineBugTest extends KernelTestCase
{
    /**
     * If this test fails, it means the bug is fixed, and we should update our
     * dependency to the fixed version.
     *
     * https://github.com/doctrine/orm/issues/4693
     */
    public function testMatchingIndexBug(): void
    {
        $repository = static::getContainer()->get(CountryMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Country> $repository */

        $country = $repository->getOrFail(1);
        $citizens = $country->getRawCitizens();

        static::assertInstanceOf(Selectable::class, $citizens);

        $matchingCitizens = $citizens->matching(Criteria::create());

        foreach ($matchingCitizens as $key => $citizen) {
            static::assertInstanceOf(Citizen::class, $citizen);
            static::assertNotSame($key, $citizen->getId());
            // should be the following if the bug is fixed
            // static::assertSame($key, $citizen->getId());
        }
    }
}
