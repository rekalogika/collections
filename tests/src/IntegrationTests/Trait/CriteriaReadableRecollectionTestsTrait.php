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

namespace Rekalogika\Collections\Tests\IntegrationTests\Trait;

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Collections\Tests\App\Entity\Citizen;
use Rekalogika\Contracts\Collections\ReadableRecollection;

/**
 * @template-covariant R of ReadableCollection<array-key,Citizen>
 */
trait CriteriaReadableRecollectionTestsTrait
{
    /**
     * @use ReadableRecollectionTestsTrait<ReadableRecollection<array-key,Citizen>>
     * @use CriteriaMinimalReadableRecollectionTestsTrait<ReadableRecollection<array-key,Citizen>>
     */
    use CriteriaMinimalReadableRecollectionTestsTrait, ReadableRecollectionTestsTrait {
        CriteriaMinimalReadableRecollectionTestsTrait::testContains insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testContainsNegative insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testContainsKey insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testContainsKeyNull insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testContainsKeyNegative insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testGet insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testGetNull insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testGetNegative insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testFetch insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testFetchNull insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testFetchNegative insteadof ReadableRecollectionTestsTrait;
        CriteriaMinimalReadableRecollectionTestsTrait::testSlice insteadof ReadableRecollectionTestsTrait;
    }
}
