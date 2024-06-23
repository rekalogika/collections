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
// namespace Rekalogika\Domain\Collections\Common\Trait;

// use Doctrine\Common\Collections\ArrayCollection;
// use Doctrine\Common\Collections\ReadableCollection;
// use Rekalogika\Domain\Collections\Internal\ExtraLazyDetector;

// /**
//  * @template TKey of array-key
//  * @template-covariant T
//  */
// trait SafetyCheckTrait
// {
//     private ?bool $isSafe = null;
//     private ?bool $isSafeWithKeys = null;

//     /**
//      * @return ReadableCollection<TKey,T>
//      */
//     abstract private function getRealCollection(): ReadableCollection;

//     private function isSafe(): bool
//     {
//         if ($this->isSafe !== null) {
//             return $this->isSafe;
//         }

//         if ($this->getRealCollection() instanceof ArrayCollection) {
//             return $this->isSafe = true;
//         }

//         return $this->isSafe = ExtraLazyDetector::isExtraLazy($this->getRealCollection());
//     }

//     private function isSafeWithKeys(): bool
//     {
//         if ($this->isSafeWithKeys !== null) {
//             return $this->isSafeWithKeys;
//         }

//         return $this->isSafeWithKeys = ExtraLazyDetector::hasIndexBy($this->getRealCollection());
//     }
// }
