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

namespace Rekalogika\Collections\Tests\IntegrationTests\Base;

use Doctrine\ORM\EntityManagerInterface;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\Exception\OverflowException;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @template-covariant R of object
 */
abstract class BaseRecollectionTestCase extends KernelTestCase
{
    /**
     * @return R
     */
    abstract protected function getObject(): mixed;
    abstract protected function isSafe(): bool;
    abstract protected function isSingleton(): bool;
    abstract protected function getExpectedTotal(): int;

    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function testSafety(): void
    {
        if (!$this->isSafe()) {
            $this->expectException(OverflowException::class);
        }
    }

    protected function getOne(): Citizen
    {
        $object = $this->getObject();
        static::assertInstanceOf(PageableInterface::class, $object);

        foreach ($object->getPages() as $page) {
            /** @var mixed $citizen */
            foreach ($page as $citizen) {
                static::assertInstanceOf(Citizen::class, $citizen);
                return $citizen;
            }
        }

        throw new \RuntimeException('No citizen found');
    }

    public function testSingleton(): void
    {
        $object = $this->getObject();
        static::assertInstanceOf(PageableInterface::class, $object);

        if ($this->isSingleton()) {
            $this->assertSame($object, $this->getObject());
        } else {
            $this->assertNotSame($object, $this->getObject());
        }
    }

}
