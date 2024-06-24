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

use Doctrine\ORM\EntityNotFoundException;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\MinimalReadableRepository;

/**
 * @template-covariant R of MinimalReadableRepository<array-key,Citizen>
 */
trait MinimalReadableRepositoryTestsTrait
{
    /** @use MinimalReadableRecollectionTestsTrait<R> */
    use MinimalReadableRecollectionTestsTrait;

    public function testReference(): void
    {
        $citizen = $this->getObject()->reference(1);
        static::assertInstanceOf(Citizen::class, $citizen);
        $name = $citizen->getName();
        static::assertIsString($name);
    }

    public function testReferenceNegative(): void
    {
        $citizen = $this->getObject()->reference(9999999);
        $this->expectException(EntityNotFoundException::class);

        $name = $citizen->getName();
    }
}
