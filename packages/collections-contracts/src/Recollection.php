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
 * @template T
 * @extends Collection<TKey,T>
 * @extends ReadableRecollection<TKey,T>
 */
interface Recollection extends ReadableRecollection, Collection
{
    //
    // Overridden methods, to widen keys in parameters to accommodate Uuid key
    // types
    //

    /**
     * @param mixed $key
     * @return T|null
     */
    #[\Override]
    public function remove(mixed $key): mixed;

    /**
     * @param mixed $key
     * @param T $value
     */
    #[\Override]
    public function set(mixed $key, mixed $value): void;

    // /**
    //  * @param \Closure(T,mixed):bool $p
    //  * @return Collection<TKey,T>
    //  */
    // public function filter(\Closure $p): Collection;

    // /**
    //  * @param \Closure(mixed,T):bool $p
    //  * @return array{0: Collection<TKey,T>, 1: Collection<TKey,T>}
    //  */
    // public function partition(\Closure $p): array;
}
