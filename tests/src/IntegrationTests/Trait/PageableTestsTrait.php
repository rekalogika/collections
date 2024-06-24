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
use Rekalogika\Contracts\Rekapager\PageableInterface;

/**
 * @template R of PageableInterface<array-key,Citizen>
 */
trait PageableTestsTrait
{
    public function testPageable(): void
    {
        $page = $this->getObject()->getFirstPage();
        static::assertCount(50, $page);
    }
}
