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

namespace Rekalogika\Collections\ORM;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\Collections\ORM\Trait\MinimalRepositoryTrait;
use Rekalogika\Collections\ORM\Trait\QueryBuilderPageableTrait;
use Rekalogika\Collections\ORM\Trait\RepositoryDxTrait;
use Rekalogika\Contracts\Collections\Exception\InvalidArgumentException;
use Rekalogika\Contracts\Collections\MinimalRepository;
use Rekalogika\Domain\Collections\Common\Configuration;
use Rekalogika\Domain\Collections\Common\Count\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\ParameterUtil;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Rekapager\Adapter\Common\SeekMethod;

/**
 * @template TKey of array-key
 * @template T of object
 * @implements MinimalRepository<TKey,T>
 */
abstract class AbstractMinimalRepository implements MinimalRepository
{
    /**
     * @use QueryBuilderPageableTrait<array-key,T>
     */
    use QueryBuilderPageableTrait;

    /**
     * @use MinimalRepositoryTrait<array-key,T>
     */
    use MinimalRepositoryTrait;

    /**
     * @use RepositoryDxTrait<array-key,T>
     */
    use RepositoryDxTrait;

    private readonly QueryBuilder $queryBuilder;

    /**
     * @var non-empty-array<string,Order>
     */
    private readonly array $orderBy;

    private readonly ?string $indexBy;

    private ?EntityManagerInterface $entityManager = null;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage;

    /**
     * @param class-string<T> $class
     * @param int<1,max> $itemsPerPage
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param null|LockMode|LockMode::* $lockMode
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly string $class,
        array|string|null $orderBy = null,
        ?int $itemsPerPage = null,
        private readonly ?CountStrategy $count = null,
        private readonly ?Pagination $pagination = null,
        private readonly SeekMethod $seekMethod = SeekMethod::Approximated,
        private readonly LockMode|int|null $lockMode = null,
    ) {
        $this->itemsPerPage = $itemsPerPage ?? Configuration::$defaultItemsPerPage;

        // set index by
        $identifiers = $this->getEntityManager()
            ->getClassMetadata($this->class)
            ->getIdentifier();

        if (\count($identifiers) !== 1) {
            throw new InvalidArgumentException('Entity with composite primary key is not supported');
        }

        $this->indexBy = $identifiers[0];

        // set orderBy
        $this->orderBy = ParameterUtil::normalizeOrderBy($orderBy);

        // set query builder
        $criteria = Criteria::create()->orderBy($this->orderBy);

        $this->queryBuilder = $this
            ->createQueryBuilder('e', 'e.' . $this->indexBy)
            ->addCriteria($criteria);
    }

    /**
     * @return class-string<T>
     */
    private function getClass(): string
    {
        /** @var class-string<T> */
        return $this->class;
    }

    private function getCountStrategy(): CountStrategy
    {
        return $this->count ?? ParameterUtil::getDefaultCountStrategyForMinimalClasses();
    }

    /**
     * @return null|int<1,max>
     */
    // @phpstan-ignore-next-line
    private function getSoftLimit(): ?int
    {
        return null;
    }

    /**
     * @return null|int<1,max>
     */
    // @phpstan-ignore-next-line
    private function getHardLimit(): ?int
    {
        return null;
    }

    /**
     * @param int<1,max> $itemsPerPage
     */
    #[\Override]
    public function withItemsPerPage(int $itemsPerPage): static
    {
        $instance = clone $this;
        $instance->itemsPerPage = $itemsPerPage;
        $instance->pageable = null;

        return $instance;
    }
}
