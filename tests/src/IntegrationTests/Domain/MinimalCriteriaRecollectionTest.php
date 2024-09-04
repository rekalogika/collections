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

namespace Rekalogika\Collections\Tests\IntegrationTests\Domain;

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\App\MinimalRepository\CountryMinimalRepository;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\MinimalReadableRecollectionTestsTrait;
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;
use Rekalogika\Contracts\Collections\MinimalRepository;

/**
 * @extends BaseRecollectionTestCase<MinimalReadableRecollection<int,Citizen>>
 */
class MinimalCriteriaRecollectionTest extends BaseRecollectionTestCase
{
    /** @use MinimalReadableRecollectionTestsTrait<MinimalReadableRecollection<array-key,Citizen>> */
    use MinimalReadableRecollectionTestsTrait;

    #[\Override]
    protected function isSingleton(): bool
    {
        return true;
    }

    #[\Override]
    protected function getExpectedTotal(): int
    {
        return 550;
    }

    #[\Override]
    protected function isSafe(): bool
    {
        return true;
    }

    #[\Override]
    protected function getObject(): MinimalReadableRecollection
    {
        $repository = static::getContainer()->get(CountryMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Country> $repository */

        $country = $repository->fetch(2);
        $citizens = $country->getWorkingAgeCitizensInMinimalRecollection(
            $this->getPaginationType(),
        );

        static::assertInstanceOf(MinimalReadableRecollection::class, $citizens);

        return $citizens;
    }
}
