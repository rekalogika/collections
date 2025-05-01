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

use Rekalogika\Collections\Tests\IntegrationTests\Domain\MinimalCriteriaRecollectionTest;
use Rekalogika\Domain\Collections\Common\Pagination;

final class MinimalCriteriaRecollectionOffsetTest extends MinimalCriteriaRecollectionTest
{
    #[\Override]
    protected function getPaginationType(): Pagination
    {
        return Pagination::Offset;
    }
}
