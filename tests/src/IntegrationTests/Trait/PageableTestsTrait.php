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
use Rekalogika\Contracts\Rekapager\Exception\LimitException;
use Rekalogika\Contracts\Rekapager\PageableInterface;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Rekapager\Keyset\Contracts\KeysetPageIdentifier;
use Rekalogika\Rekapager\Offset\Contracts\PageNumber;

/**
 * @template-covariant R of PageableInterface<array-key,Citizen>
 */
trait PageableTestsTrait
{
    public function testFirstPage(): void
    {
        // $expected = ($this->getExpectedTotal() % 50) ?: 50;
        $expected = $this->getExpectedTotal() > 50 ? 50 : $this->getExpectedTotal();

        $page = $this->getObject()->getFirstPage();
        static::assertCount($expected, $page);
    }

    public function testPageableIteration(): void
    {
        if (
            $this->getPaginationType() === Pagination::Offset
            && $this->getExpectedTotal() > 5000
        ) {
            $this->expectException(LimitException::class);
        }

        $i = 0;
        foreach ($this->getObject()->getPages() as $page) {
            foreach ($page as $key => $citizen) {
                static::assertInstanceOf(Citizen::class, $citizen);
                static::assertEquals($key, $citizen->getId());
                $i++;
            }
        }

        static::assertEquals($this->getExpectedTotal(), $i);
    }

    public function testPaginationType(): void
    {
        $object = $this->getObject();

        $firstPage = $object->getFirstPage();
        $identifier = $firstPage->getPageIdentifier();

        if ($this->getPaginationType() === Pagination::Keyset) {
            $this->assertInstanceOf(KeysetPageIdentifier::class, $identifier);
        } else {
            $this->assertInstanceOf(PageNumber::class, $identifier);
        }
    }
}
