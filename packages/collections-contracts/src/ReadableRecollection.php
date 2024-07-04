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
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Rekalogika\Contracts\Rekapager\PageableInterface;

/**
 * @template TKey of array-key
 * @template-covariant T
 * @extends PageableInterface<TKey,T>
 * @extends ReadableCollection<TKey,T>
 */
interface ReadableRecollection extends PageableInterface, ReadableCollection
{
    //
    // Overridden methods, to widen keys in parameters to accommodate Uuid key
    // types
    //

    /**
     * @param mixed $key
     * @return bool
     */
    #[\Override]
    public function containsKey(mixed $key): bool;

    /**
     * @param mixed $key
     * @return T|null
     */
    #[\Override]
    public function get(mixed $key): mixed;

    //
    // Methods
    //

    /**
     * @param mixed $key
     * @return T
     * @throws NotFoundException
     */
    public function fetch(mixed $key): mixed;

    public function refreshCount(): void;
}
