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

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;

/**
 * @template TKey of array-key
 * @template-covariant T
 */
trait MinimalReadableRecollectionTrait
{
    /**
     * @use PageableTrait<TKey,T>
     */
    use PageableTrait;

    use RefreshCountTrait;

    /**
     * @use FetchTrait<TKey,T>
     */
    use FetchTrait;

    /**
     * @return ReadableCollection<TKey,T>
     */
    abstract private function getRealCollection(): ReadableCollection;

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    final public function contains(mixed $element): bool
    {
        return $this->getRealCollection()->contains($element);
    }

    final public function containsKey(mixed $key): bool
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        if ($key === null) {
            return false;
        }

        return $this->getRealCollection()->containsKey($key);
    }

    /**
     * @return T|null
     */
    final public function get(mixed $key): mixed
    {
        /** @var TKey */
        $key = ParameterUtil::transformInputToKey($this->keyTransformer, $key);

        if ($key === null) {
            return null;
        }

        return $this->getRealCollection()->get($key);
    }
}
