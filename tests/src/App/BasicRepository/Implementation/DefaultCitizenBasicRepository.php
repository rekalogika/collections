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

use Doctrine\Common\Collections\Order;
use Rekalogika\Collections\ORM\AbstractBasicRepository;
use Rekalogika\Collections\ORM\Configuration\BasicRepositoryConfiguration;
use Rekalogika\Collections\Tests\App\BasicRepository\CitizenBasicRepository;
use Rekalogika\Collections\Tests\App\Entity\Citizen;

/**
 * @extends AbstractBasicRepository<array-key,Citizen>
 */
class DefaultCitizenBasicRepository extends AbstractBasicRepository implements CitizenBasicRepository
{
    protected function configure(): BasicRepositoryConfiguration
    {
        return new BasicRepositoryConfiguration(
            class: Citizen::class,
            identifier: 'id',
            orderBy: ['id' => Order::Ascending]
        );
    }
}
