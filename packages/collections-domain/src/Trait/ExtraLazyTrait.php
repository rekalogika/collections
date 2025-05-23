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

namespace Rekalogika\Domain\Collections\Trait;

use Doctrine\Common\Collections\Collection;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait ExtraLazyTrait
{
    /**
     * @use ReadableExtraLazyTrait<TKey,T>
     */
    use ReadableExtraLazyTrait;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getRealCollection(): Collection;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getNewCollection(): Collection;

    /**
     * @param TKey $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        if ($this->isSafeWithIndex()) {
            return $this->getRealCollection()->containsKey($offset);
        }

        return $this->getSafeCollection()->containsKey($offset);
    }

    /**
     * @param TKey $offset
     * @return T|null
     */
    final public function offsetGet(mixed $offset): mixed
    {
        if ($this->isSafeWithIndex()) {
            return $this->getRealCollection()->get($offset);
        }

        return $this->getSafeCollection()->get($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!isset($offset)) {
            $this->getRealCollection()->offsetSet(null, $value);
            $this->getNewCollection()->offsetSet(null, $value);

            return;
        }

        /** @var TKey $offset */

        $this->ensureSafety();
        $this->getRealCollection()->set($offset, $value);
    }

    /**
     * @param T $element
     */
    final public function add(mixed $element): void
    {
        $this->getRealCollection()->add($element);
        $this->getNewCollection()->add($element);
    }
}
