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

    use RefreshableCountTrait;

    /**
     * @use FindFetchTrait<TKey,T>
     */
    use FindFetchTrait;

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

    /**
     * @param TKey $key
     */
    final public function containsKey(string|int $key): bool
    {
        return $this->getRealCollection()->containsKey($key);
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    final public function get(string|int $key): mixed
    {
        return $this->getRealCollection()->get($key);
    }
}
