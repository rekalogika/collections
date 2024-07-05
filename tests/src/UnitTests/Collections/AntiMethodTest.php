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

namespace Rekalogika\Collections\Tests\UnitTests\Collections;

use PHPUnit\Framework\TestCase;
use Rekalogika\Collections\ORM\AbstractMinimalRepository;
use Rekalogika\Collections\ORM\QueryPageable;
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;
use Rekalogika\Contracts\Collections\MinimalReadableRepository;
use Rekalogika\Contracts\Collections\MinimalRecollection;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Contracts\Collections\PageableRecollection;
use Rekalogika\Domain\Collections\MinimalCriteriaRecollection;
use Rekalogika\Domain\Collections\MinimalRecollectionDecorator;

class AntiMethodTest extends TestCase
{
    /**
     * @param class-string $class
     * @dataProvider provideClasses
     */
    public function testDoesNotHaveCountMethod(string $class): void
    {
        $this->assertFalse(method_exists($class, 'count'));
    }

    /**
     * @return iterable<array-key,array<array-key,class-string>>
     */
    public static function provideClasses(): iterable
    {
        yield [MinimalReadableRecollection::class];
        yield [MinimalReadableRepository::class];
        yield [MinimalRecollection::class];
        yield [MinimalRepository::class];
        yield [PageableRecollection::class];
        yield [MinimalCriteriaRecollection::class];
        yield [MinimalRecollectionDecorator::class];
        yield [AbstractMinimalRepository::class];
        yield [QueryPageable::class];
    }
}
