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

namespace Rekalogika\Contracts\Collections;

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Contracts\Rekapager\PageableInterface;

/**
 * @template TKey of array-key
 * @template T
 * @extends PageableInterface<TKey,T>
 * @extends ReadableCollection<TKey,T>
 */
interface ReadablePageableCollection extends PageableInterface, ReadableCollection
{
    public function refreshCount(): void;
}
