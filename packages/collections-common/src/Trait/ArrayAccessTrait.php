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

namespace Rekalogika\Domain\Collections\Common\Trait;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;

/**
 * @template TKey of array-key
 * @template T
 */
trait ArrayAccessTrait
{
    /**
     * @return ReadableCollection<TKey,T>
     */
    abstract private function getSafeCollection(): ReadableCollection;
    abstract private function ensureSafety(): void;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getRealCollection(): Collection;

    /**
     * @param TKey $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        // return $this->getSafeCollection()->containsKey($offset);

        return $this->containsKey($offset);
    }

    /**
     * @param TKey $offset
     * @return T|null
     */
    final public function offsetGet(mixed $offset): mixed
    {
        // return $this->getSafeCollection()->get($offset);

        return $this->get($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureSafety();

        // $this->getRealCollection()->offsetSet($offset, $value);
        $this->add($value);
    }

    /**
     * @param TKey $offset
     */
    final public function offsetUnset(mixed $offset): void
    {
        $this->ensureSafety();

        // $this->getRealCollection()->offsetUnset($offset);
        $this->remove($offset);
    }
}
