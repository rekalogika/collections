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
use Rekalogika\Contracts\Collections\Recollection;

/**
 * @template-covariant R of Recollection<array-key,Citizen>
 */
trait RecollectionTestsTrait
{
    /** @use ReadableRecollectionTestsTrait<R> */
    use ReadableRecollectionTestsTrait;

    /** @use CollectionTestsTrait<R> */
    use CollectionTestsTrait;
}
