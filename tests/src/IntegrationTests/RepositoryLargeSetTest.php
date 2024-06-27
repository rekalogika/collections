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

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\Repository\CitizenRepository;
use Rekalogika\Collections\Tests\IntegrationTests\Base\BaseRecollectionTestCase;
use Rekalogika\Collections\Tests\IntegrationTests\Trait\RepositoryTestsTrait;
use Rekalogika\Contracts\Collections\Repository;

/**
 * @extends BaseRecollectionTestCase<Repository<array-key,Citizen>>
 */
class RepositoryLargeSetTest extends BaseRecollectionTestCase
{
    /** @use RepositoryTestsTrait<Repository<array-key,Citizen>> */
    use RepositoryTestsTrait;

    protected function isSingleton(): bool
    {
        return true;
    }

    protected function getExpectedTotal(): int
    {
        return 7740;
    }

    protected function isSafe(): bool
    {
        return false;
    }

    protected function getObject(): Repository
    {
        $repository = static::getContainer()->get(CitizenRepository::class);
        static::assertInstanceOf(Repository::class, $repository);

        /** @var Repository<array-key,Citizen> $repository */

        return $repository;
    }
}
