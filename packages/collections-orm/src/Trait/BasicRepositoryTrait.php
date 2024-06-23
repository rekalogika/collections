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

use Rekalogika\Contracts\Collections\Exception\NotFoundException;

/**
 * @template TKey of array-key
 * @template T of object
 *
 * @internal
 */
trait BasicRepositoryTrait
{
    /**
     * @param TKey $key
     * @return T
     */
    public function reference(int|string $key): object
    {
        return $this->getEntityManager()
            ->getReference($this->getClass(), $key)
            ?? throw new NotFoundException('Entity not found');
    }

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    public function contains(mixed $element): bool
    {
        if (!\is_object($element)) {
            return false;
        }

        return $this->getEntityManager()->contains($element);
    }

    /**
     * @param TKey $key
     */
    public function containsKey(string|int $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    public function get(string|int $key): mixed
    {
        return $this->getEntityManager()->find($this->getClass(), $key);
    }

    /**
     * @param TKey $key
     * @return T
     * @throws NotFoundException
     */
    public function getOrFail(string|int $key): mixed
    {
        return $this->get($key) ?? throw new NotFoundException('Entity not found');
    }

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
     * @param TKey $key
     * @return T|null
     */
    public function remove(string|int $key): mixed
    {
        $element = $this->get($key);

        if ($element === null) {
            return null;
        }

        $this->getEntityManager()->remove($element);

        return $element;
    }
}
