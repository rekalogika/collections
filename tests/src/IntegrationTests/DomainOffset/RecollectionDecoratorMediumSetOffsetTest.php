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

namespace Rekalogika\Collections\Tests\IntegrationTests\DomainOffset;

use Rekalogika\Collections\Tests\IntegrationTests\Domain\RecollectionDecoratorMediumSetTest;
use Rekalogika\Domain\Collections\Common\Pagination;

class RecollectionDecoratorMediumSetOffsetTest extends RecollectionDecoratorMediumSetTest
{
    #[\Override]
    protected function getPaginationType(): Pagination
    {
        return Pagination::Offset;
    }
}
