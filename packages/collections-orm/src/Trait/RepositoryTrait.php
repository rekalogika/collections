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

use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Domain\Collections\Common\Internal\KeyTransformerUtil;

/**
 * @template TKey of array-key
 * @template T of object
 *
 * @internal
 */
trait RepositoryTrait
{
    /**
     * @use ReadableRepositoryTrait<TKey,T>
     */
    use ReadableRepositoryTrait;

    /**
     * @use MinimalRepositoryTrait<TKey,T>
     */
    use MinimalRepositoryTrait;

    final public function clear(): void
    {
        foreach ($this->getSafeCollection() as $element) {
            $this->removeElement($element);
        }
    }

    /**
     * @param mixed $offset
     */
    final public function offsetExists(mixed $offset): bool
    {
        /** @var TKey */
        $offset = KeyTransformerUtil::transformInputToKey($this->keyTransformer, $offset);

        return $this->containsKey($offset);
    }

    /**
     * @param mixed $offset
     * @return T|null
     */
    final public function offsetGet(mixed $offset): mixed
    {
        /** @var TKey */
        $offset = KeyTransformerUtil::transformInputToKey($this->keyTransformer, $offset);

        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param T $value
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset !== null) {
            throw new InvalidArgumentException('This collection does not support setting by key');
        }

        $this->add($value);
    }

    /**
     * @param mixed $offset
     */
    final public function offsetUnset(mixed $offset): void
    {
        /** @var TKey */
        $offset = KeyTransformerUtil::transformInputToKey($this->keyTransformer, $offset);

        $this->remove($offset);
    }

    /**
     * @param mixed $key
     * @param T $value
     */
    final public function set(mixed $key, mixed $value): void
    {
        $this->add($value);
    }
}
