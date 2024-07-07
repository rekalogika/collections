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

namespace Rekalogika\Collections\Tests\UnitTests\Collections\Fixtures;

class Country
{
    private string $foo = 'bar';

    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function x(): string
    {
        return $this->foo;
    }
}
