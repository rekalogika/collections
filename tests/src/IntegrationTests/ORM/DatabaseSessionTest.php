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

use Doctrine\ORM\EntityManagerInterface;
use Rekalogika\Collections\ORM\DatabaseSession;
use Rekalogika\Collections\ORM\Implementation\DefaultDatabaseSession;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @runTestsInSeparateProcesses
 */
final class DatabaseSessionTest extends KernelTestCase
{
    public function testDatabaseSessionIsRegistered(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $databaseSession = $container->get(DatabaseSession::class);

        $this->assertInstanceOf(DefaultDatabaseSession::class, $databaseSession);
    }

    public function testDatabaseSessionFlushAndClear(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $databaseSession = $container->get(DatabaseSession::class);

        $citizen = new Citizen();
        $citizen->setName('Test Citizen');
        $citizen->setAge(30);
        $entityManager->persist($citizen);

        $databaseSession->flush();

        $this->assertNotNull($citizen->getId());

        $databaseSession->clear();

        $this->assertFalse($entityManager->contains($citizen));

        // Remove the entity to avoid affecting other tests
        $entityManager->remove($entityManager->find(Citizen::class, $citizen->getId()));
        $databaseSession->flush();
    }

    public function testDatabaseSessionWithArgumentBinding(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        // Test that argument binding works
        $databaseSession = $container->get(DatabaseSession::class . ' $defaultDatabaseSession');

        $this->assertInstanceOf(DefaultDatabaseSession::class, $databaseSession);
    }
}
