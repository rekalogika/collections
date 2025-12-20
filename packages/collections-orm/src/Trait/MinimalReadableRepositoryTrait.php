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
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Rekalogika\Domain\Collections\Common\Trait\FetchTrait;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;
use Rekalogika\Domain\Collections\Common\Trait\RefreshCountTrait;

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

    use RefreshCountTrait;

    /**
     * @use FetchTrait<TKey,T>
     */
    use FetchTrait;

    abstract protected function getEntityManager(): EntityManagerInterface;

    /**
     * @return class-string
     */
    abstract protected function getClass(): string;

    /**
     * @return T
     */
    public function reference(mixed $key): object
    {
        if ($key === null) {
            throw new InvalidArgumentException('Key cannot be null');
        }

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

    public function containsKey(mixed $key): bool
    {
        if ($key === null) {
            return false;
        }

        return $this->get($key) !== null;
    }

    /**
     * @return T|null
     */
    public function get(mixed $key): mixed
    {
        if ($key === null) {
            return null;
        }

        return $this->getEntityManager()->find($this->getClass(), $key);
    }
}
