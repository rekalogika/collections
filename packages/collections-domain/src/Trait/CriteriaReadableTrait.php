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
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait CriteriaReadableTrait
{
    /**
     * @use SafetyCheckTrait<TKey,T>
     */
    use SafetyCheckTrait;

    /**
     * @return Collection<TKey,T>
     */
    abstract private function getSafeCollection(): Collection;

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return $this->getSafeCollection()->contains($element);
    }

    /**
     * @param mixed $key
     */
    final public function containsKey(mixed $key): bool
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        return $this->getSafeCollection()->containsKey($key);
    }

    /**
     * @param mixed $key
     * @return T|null
     */
    final public function get(mixed $key): mixed
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        return $this->getSafeCollection()->get($key);
    }

    /**
     * @return array<TKey,T>
     */
    final public function slice(int $offset, ?int $length = null): array
    {
        return $this->getSafeCollection()->slice($offset, $length);
    }
}
