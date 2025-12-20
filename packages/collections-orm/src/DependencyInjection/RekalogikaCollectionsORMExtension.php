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
use Symfony\Component\DependencyInjection\Reference;

final class RekalogikaCollectionsORMExtension extends Extension
{
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!class_exists(DoctrineBundle::class)) {
            return;
        }

        // Check if we're in test or dev environment
        $isTestOrDev = \in_array($container->getParameter('kernel.environment'), ['test', 'dev'], true);

        // Get the list of entity managers from container parameters
        // This will be set by DoctrineBundle
        $entityManagers = $container->hasParameter('doctrine.entity_managers')
            ? $container->getParameter('doctrine.entity_managers')
            : [];

        if (!\is_array($entityManagers) || empty($entityManagers)) {
            // Fallback: register a single DatabaseSession with autowiring
            $container
                ->register(DefaultDatabaseSession::class)
                ->setAutowired(true)
                ->setPublic($isTestOrDev);

            $container->setAlias(DatabaseSession::class, DefaultDatabaseSession::class)
                ->setPublic($isTestOrDev);
            return;
        }

        $defaultEntityManager = $container->hasParameter('doctrine.default_entity_manager')
            ? $container->getParameter('doctrine.default_entity_manager')
            : null;

        foreach ($entityManagers as $name => $entityManagerServiceId) {
            \assert(\is_string($entityManagerServiceId));

            $serviceId = \sprintf('rekalogika.collections_orm.database_session.%s', $name);

            $container
                ->register($serviceId, DefaultDatabaseSession::class)
                ->setArguments([new Reference($entityManagerServiceId)])
                ->setPublic($isTestOrDev);

            // Create an alias with parameter name for argument binding
            $parameterName = \sprintf('%sDatabaseSession', $name);
            $container->setAlias(
                \sprintf('%s $%s', DatabaseSession::class, $parameterName),
                $serviceId,
            )->setPublic($isTestOrDev);

            // Create an alias for the default entity manager
            if ($name === $defaultEntityManager) {
                $container->setAlias(DatabaseSession::class, $serviceId)
                    ->setPublic($isTestOrDev);
                $container->setAlias(DefaultDatabaseSession::class, $serviceId)
                    ->setPublic($isTestOrDev);
            }
        }
    }
}
