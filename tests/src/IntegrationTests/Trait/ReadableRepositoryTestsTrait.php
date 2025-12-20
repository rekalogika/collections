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

namespace Rekalogika\Collections\Tests\IntegrationTests\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\ReadableRepository;

/**
 * @template-covariant R of ReadableRepository<array-key,Citizen>
 */
trait ReadableRepositoryTestsTrait
{
    /** @use ReadableRecollectionTestsTrait<R> */
    use ReadableRecollectionTestsTrait;

    public function testReference(): void
    {
        $object = $this->getOne();
        $id = $object->getId();
        static::assertNotNull($id);

        static::getContainer()->get(EntityManagerInterface::class)->clear();

        $object = $this->getObject()->reference($id);
        static::assertInstanceOf(Citizen::class, $object);
        $object->getAge();
    }

    public function testReferenceNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getObject()->reference(null);
    }
}
