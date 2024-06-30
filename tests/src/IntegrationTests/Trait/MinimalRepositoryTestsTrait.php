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

use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\MinimalRepository;

/**
 * @template-covariant R of MinimalRepository<array-key,Citizen>
 */
trait MinimalRepositoryTestsTrait
{
    /** @use MinimalReadableRepositoryTestsTrait<R> */
    use MinimalReadableRepositoryTestsTrait;

    /** @use MinimalRecollectionTestsTrait<R> */
    use MinimalRecollectionTestsTrait;

    public function testRemove(): void
    {
        $removed = $this->getObject()->remove(1);
        static::assertFalse($this->getObject()->contains($removed));
    }

    public function testRemoveNegative(): void
    {
        $removed = $this->getObject()->remove(9999999);
        static::assertNull($removed);
    }

    public function testRemoveElement(): void
    {
        $citizen = $this->getObject()->fetch(1);
        static::assertTrue($this->getObject()->removeElement($citizen));
    }

    public function testRemoveElementNegative(): void
    {
        $citizen = new Citizen();
        static::assertFalse($this->getObject()->removeElement($citizen));
    }
}
