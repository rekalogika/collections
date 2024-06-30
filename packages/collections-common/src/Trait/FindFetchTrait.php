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

use Rekalogika\Contracts\Collections\Exception\NotFoundException;

/**
 * @template TKey of array-key
 * @template T
 */
trait FindFetchTrait
{
    /**
     * @return T|null
     */
    final public function find(mixed $key): mixed
    {
        if ($key instanceof \Stringable) {
            $key = (string) $key;
        } elseif (!\is_string($key) && !\is_int($key)) {
            return null;
        }

        /** @var TKey $key */

        $result = $this->get($key);

        if ($result === null) {
            return null;
        }

        return $result;
    }

    /**
     * @return T
     * @throws NotFoundException
     */
    final public function fetch(mixed $key): mixed
    {
        $result = $this->find($key);

        if ($result === null) {
            throw new NotFoundException();
        }

        return $result;
    }
}
