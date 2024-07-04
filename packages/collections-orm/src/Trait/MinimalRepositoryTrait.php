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

namespace Rekalogika\Collections\ORM\Trait;

/**
 * @template TKey of array-key
 * @template T of object
 *
 * @internal
 */
trait MinimalRepositoryTrait
{
    /**
     * @use MinimalReadableRepositoryTrait<TKey,T>
     */
    use MinimalReadableRepositoryTrait;

    /**
     * @param T $element
     */
    public function add(mixed $element): void
    {
        $this->getEntityManager()->persist($element);
    }

    /**
     * @param T $element
     */
    public function removeElement(mixed $element): bool
    {
        if (!$this->contains($element)) {
            return false;
        }

        $this->getEntityManager()->remove($element);

        return true;
    }

    /**
     * @param mixed $key
     * @return T|null
     */
    public function remove(mixed $key): mixed
    {
        $element = $this->get($key);

        if ($element === null) {
            return null;
        }

        $this->getEntityManager()->remove($element);

        return $element;
    }
}
