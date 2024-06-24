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
use Rekalogika\Contracts\Collections\ReadableRecollection;

/**
 * @template-covariant R of ReadableRecollection<array-key,Citizen>
 */
trait ReadableRecollectionTestsTrait
{
    /** @use ReadableCollectionTestsTrait<R> */
    use ReadableCollectionTestsTrait;
}
