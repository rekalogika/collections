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

use Rekalogika\Collections\Tests\App\BasicRepository\CountryMinimalRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\ReadableRecollectionTestsTrait;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Contracts\Collections\ReadableRecollection;

/**
 * @extends BaseRecollectionTestCase<ReadableRecollection<int,Citizen>>
 */
class CriteriaRecollectionLargeSetTest extends BaseRecollectionTestCase
{
    /** @use ReadableRecollectionTestsTrait<ReadableRecollection<array-key,Citizen>> */
    use ReadableRecollectionTestsTrait;

    protected function isSingleton(): bool
    {
        return true;
    }

    protected function getExpectedTotal(): int
    {
        return 2020;
    }

    protected function isSafe(): bool
    {
        return false;
    }

    protected function getObject(): ReadableRecollection
    {
        $repository = static::getContainer()->get(CountryMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Country> $repository */

        $country = $repository->getOrFail(1);
        $citizens = $country->getWorkingAgeCitizensInRecollection();

        static::assertInstanceOf(ReadableRecollection::class, $citizens);

        return $citizens;
    }
}
