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

use Doctrine\Common\Collections\Collection;

/**
 * @template TKey of array-key
 * @template T of object
 * @extends ReadableRepository<TKey,T>
 * @extends Recollection<TKey,T>
 */
interface Repository extends ReadableRepository, Recollection
{
    //
    // Overridden methods, to resolve the conflicting return types inherited
    // from both ReadableRepository and Recollection
    //

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return Collection<TKey,U>
     */
    #[\Override]
    public function map(\Closure $func): Collection;

    /**
     * @param \Closure(T, TKey):bool $p
     * @return Collection<TKey,T>
     */
    #[\Override]
    public function filter(\Closure $p): Collection;
}
