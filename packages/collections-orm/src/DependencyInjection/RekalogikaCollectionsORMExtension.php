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

namespace Rekalogika\Collections\ORM\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Rekalogika\Collections\ORM\DatabaseSession;
use Rekalogika\Collections\ORM\Implementation\DefaultDatabaseSession;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class RekalogikaCollectionsORMExtension extends Extension
{
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!class_exists(DoctrineBundle::class)) {
            return;
        }

        $container
            ->register(DefaultDatabaseSession::class)
            ->setAutowired(true)
            ->setPublic(false);

        $container->setAlias(DatabaseSession::class, DefaultDatabaseSession::class);
    }
}
