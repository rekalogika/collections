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

namespace Rekalogika\Collections\ORM\Implementation;

use Doctrine\ORM\EntityManagerInterface;
use Rekalogika\Collections\ORM\DatabaseSession;

final readonly class DefaultDatabaseSession implements DatabaseSession
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[\Override]
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    #[\Override]
    public function clear(): void
    {
        $this->entityManager->clear();
    }
}
