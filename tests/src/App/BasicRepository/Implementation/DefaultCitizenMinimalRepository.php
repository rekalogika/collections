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

namespace Rekalogika\Collections\Tests\App\BasicRepository\Implementation;

use Rekalogika\Collections\ORM\AbstractMinimalRepository;
use Rekalogika\Collections\ORM\Configuration\MinimalRepositoryConfiguration;
use Rekalogika\Collections\Tests\App\BasicRepository\CitizenMinimalRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;

/**
 * @extends AbstractMinimalRepository<array-key,Citizen>
 */
class DefaultCitizenMinimalRepository extends AbstractMinimalRepository implements CitizenMinimalRepository
{
    protected function configure(): MinimalRepositoryConfiguration
    {
        return new MinimalRepositoryConfiguration(
            class: Citizen::class,
        );
    }
}
