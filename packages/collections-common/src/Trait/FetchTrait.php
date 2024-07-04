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
trait FetchTrait
{
    /**
     * @param mixed $key
     * @return T
     * @throws NotFoundException
     */
    final public function fetch(mixed $key): mixed
    {
        $result = $this->get($key);

        if ($result === null) {
            throw new NotFoundException();
        }

        return $result;
    }
}