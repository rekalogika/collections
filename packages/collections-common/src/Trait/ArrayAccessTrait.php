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
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

/**
 * @template TKey of array-key
 * @template T
 */
trait ArrayAccessTrait
{
    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;
    abstract private function ensureSafety(): void;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getRealCollection(): Collection;

    final public function offsetExists(mixed $offset): bool
    {
        /** @var TKey */
        $offset = ParameterUtil::transformInputToKey($this->keyTransformer, $offset);

        // return $this->getSafeCollection()->containsKey($offset);

        return $this->containsKey($offset);
    }

    /**
     * @return T|null
     */
    final public function offsetGet(mixed $offset): mixed
    {
        /** @var TKey */
        $offset = ParameterUtil::transformInputToKey($this->keyTransformer, $offset);

        // return $this->getSafeCollection()->get($offset);

        return $this->get($offset);
    }

    /**
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!isset($offset)) {
            $this->getRealCollection()->offsetSet(null, $value);

            return;
        }

        /** @var TKey */
        $offset = ParameterUtil::transformInputToKey($this->keyTransformer, $offset);

        $this->ensureSafety();

        $this->getRealCollection()->offsetSet($offset, $value);
    }

    final public function offsetUnset(mixed $offset): void
    {
        /** @var TKey */
        $offset = ParameterUtil::transformInputToKey($this->keyTransformer, $offset);

        $this->ensureSafety();

        // $this->getRealCollection()->offsetUnset($offset);
        $this->remove($offset);
    }
}
