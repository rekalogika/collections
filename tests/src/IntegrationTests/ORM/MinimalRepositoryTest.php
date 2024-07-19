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

namespace Rekalogika\Collections\Tests\IntegrationTests\ORM;

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\MinimalRepository\CitizenMinimalRepository;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\MinimalRepositoryTestsTrait;
use Rekalogika\Contracts\Collections\MinimalRepository;

/**
 * @extends BaseRecollectionTestCase<MinimalRepository<array-key,Citizen>>
 */
class MinimalRepositoryTest extends BaseRecollectionTestCase
{
    /** @use MinimalRepositoryTestsTrait<MinimalRepository<array-key,Citizen>> */
    use MinimalRepositoryTestsTrait;

    #[\Override]
    protected function isSingleton(): bool
    {
        return true;
    }

    #[\Override]
    protected function getExpectedTotal(): int
    {
        return 7740;
    }

    #[\Override]
    protected function isSafe(): bool
    {
        return false;
    }

    #[\Override]
    protected function getObject(): MinimalRepository
    {
        $repository = static::getContainer()->get(CitizenMinimalRepository::class);
        static::assertInstanceOf(MinimalRepository::class, $repository);

        /** @var MinimalRepository<array-key,Citizen> $repository */

        return $repository;
    }
}
