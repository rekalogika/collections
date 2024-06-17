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

namespace Rekalogika\Domain\Collections\Common;

enum CountStrategy
{
    /**
     * The Collection does not support counting, calling count() will throw an
     * exception.
     */
    case Restrict;

    /**
     * The Collection will delegate counting to its underlying Collection. It
     * will call count() on the underlying Collection to get the count.
     */
    case Delegate;

    /**
     * The count is supplied by the caller.
     */
    case Provided;
}
