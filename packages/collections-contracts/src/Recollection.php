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

    #[\Override]
    public function offsetExists(mixed $offset): bool;

    /**
     * @return T|null
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed;

    /**
     * @param T $value
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void;

    #[\Override]
    public function offsetUnset(mixed $offset): void;
}
