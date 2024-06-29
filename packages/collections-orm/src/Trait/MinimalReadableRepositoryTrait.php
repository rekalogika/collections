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

use Doctrine\ORM\EntityManagerInterface;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
use Rekalogika\Domain\Collections\Common\Trait\RefreshableCountTrait;

/**
 * @template TKey of array-key
 * @template-covariant T of object
 *
 * @internal
 */
trait MinimalReadableRepositoryTrait
{
    /**
     * @use PageableTrait<TKey,T>
     */
    use PageableTrait;

    use RefreshableCountTrait;

    abstract private function getEntityManager(): EntityManagerInterface;

    /**
     * @return class-string
     */
    abstract private function getClass(): string;

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
}
