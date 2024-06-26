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

namespace Rekalogika\Collections\Tests\App\Repository\Implementation;

use Rekalogika\Collections\ORM\AbstractRepository;
use Rekalogika\Collections\ORM\Configuration\RepositoryConfiguration;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\App\Repository\CountryRepository;

/**
 * @extends AbstractRepository<array-key,Country>
 */
class DefaultCountryRepository extends AbstractRepository implements CountryRepository
{
    protected function configure(): RepositoryConfiguration
    {
        return new RepositoryConfiguration(
            class: Country::class,
        );
    }
}
