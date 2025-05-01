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

use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\Collections\ORM\AbstractRepository;
use Rekalogika\Collections\Tests\App\Entity\Country;
use Rekalogika\Collections\Tests\App\Repository\CountryRepository;

/**
 * @extends AbstractRepository<array-key,Country>
 */
final class DefaultCountryRepository extends AbstractRepository implements CountryRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct(
            managerRegistry: $managerRegistry,
            class: Country::class,
        );
    }
}
