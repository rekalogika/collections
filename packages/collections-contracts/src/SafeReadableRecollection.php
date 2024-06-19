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

use Rekalogika\Contracts\Rekapager\PageableInterface;

/**
 * @template TKey of array-key
 * @template-covariant T
 * @extends PageableInterface<TKey,T>
 */
interface SafeReadableRecollection extends PageableInterface
{
    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    public function contains(mixed $element): bool;

    /**
     * @param TKey $key
     */
    public function containsKey(string|int $key): bool;

    /**
     * @param TKey $key
     * @return T|null
     */
    public function get(string|int $key): mixed;
}
