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

use Doctrine\ORM\PersistentCollection;
use Rekalogika\Collections\Tests\App\BasicRepository\CountryMinimalRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\MinimalRecollectionTestsTrait;
use Rekalogika\Contracts\Collections\MinimalRecollection;
use Rekalogika\Contracts\Collections\MinimalRepository;

/**
 * @extends BaseRecollectionTestCase<MinimalRecollection<int,Citizen>>
 */
class MinimalRecollectionDecoratorTest extends BaseRecollectionTestCase
{
    /** @use MinimalRecollectionTestsTrait<MinimalRecollection<array-key,Citizen>> */
    use MinimalRecollectionTestsTrait;

    protected function isSingleton(): bool
    {
        return true;
    }

    protected function getExpectedTotal(): int
    {
        return 2050;
    }

    protected function isSafe(): bool
    {
        return true;
    }

    protected function getObject(): MinimalRecollection
    {
        $repository = static::getContainer()->get(CountryMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Country> $repository */

        $country = $repository->getOrFail(1);
        $citizens = $country->getCitizensInMinimalRecollection();

        static::assertInstanceOf(MinimalRecollection::class, $citizens);
        static::assertInstanceOf(PersistentCollection::class, $country->getRawCitizens());

        return $citizens;
    }
}
