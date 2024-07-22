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

use Doctrine\ORM\PersistentCollection;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\App\MinimalRepository\CountryMinimalRepository;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\RecollectionTestsTrait;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Contracts\Collections\Recollection;

/**
 * @extends BaseRecollectionTestCase<Recollection<int,Citizen>>
 */
class RecollectionDecoratorTest extends BaseRecollectionTestCase
{
    /** @use RecollectionTestsTrait<Recollection<array-key,Citizen>> */
    use RecollectionTestsTrait;

    #[\Override]
    protected function isSingleton(): bool
    {
        return true;
    }

    #[\Override]
    protected function getExpectedTotal(): int
    {
        return 6060;
    }

    #[\Override]
    protected function isSafe(): bool
    {
        return false;
    }

    #[\Override]
    protected function getObject(): Recollection
    {
        $repository = static::getContainer()->get(CountryMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Country> $repository */

        $country = $repository->fetch(1);
        $citizens = $country->getCitizensInRecollection(
            $this->getPaginationType()
        );

        static::assertInstanceOf(Recollection::class, $citizens);
        static::assertInstanceOf(PersistentCollection::class, $country->getRawCitizens());

        return $citizens;
    }
}
