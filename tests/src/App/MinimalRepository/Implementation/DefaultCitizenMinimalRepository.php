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

namespace Rekalogika\Collections\Tests\App\MinimalRepository\Implementation;

use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\Collections\ORM\AbstractMinimalRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Collections\Tests\App\MinimalRepository\CitizenMinimalRepository;

/**
 * @extends AbstractMinimalRepository<array-key,Citizen>
 */
final class DefaultCitizenMinimalRepository extends AbstractMinimalRepository implements CitizenMinimalRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct(
            managerRegistry: $managerRegistry,
            class: Citizen::class,
        );
    }
}
