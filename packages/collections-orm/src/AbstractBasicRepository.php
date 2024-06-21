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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Rekalogika\Collections\ORM\Configuration\BasicRepositoryConfiguration;
use Rekalogika\Collections\ORM\Trait\QueryBuilderTrait;
use Rekalogika\Contracts\Collections\BasicRepository;
use Rekalogika\Contracts\Collections\Exception\NotFoundException;
use Rekalogika\Domain\Collections\Common\CountStrategy;
use Rekalogika\Domain\Collections\Common\Internal\OrderByUtil;
use Rekalogika\Domain\Collections\Common\Trait\PageableTrait;

/**
 * @template TKey of array-key
 * @template T of object
 * @implements BasicRepository<TKey,T>
 */
abstract class AbstractBasicRepository implements BasicRepository
{
    /**
     * @use QueryBuilderTrait<array-key,T>
     */
    use QueryBuilderTrait;

    /**
     * @use PageableTrait<array-key,T>
     */
    use PageableTrait;

    private QueryBuilder $queryBuilder;

    private ?int $count = 0;

    /**
     * @var non-empty-array<string,Order>
     */
    private array $orderBy;

    /**
     * @var int<1,max>
     */
    private int $itemsPerPage = 50;

    private CountStrategy $countStrategy = CountStrategy::Restrict;

    final public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $configuration = new BasicRepositoryConfiguration();
        $this->configure($configuration);

        // set configuration

        $this->orderBy = OrderByUtil::normalizeOrderBy($configuration->getOrderBy());
        $this->itemsPerPage = $configuration->getItemsPerPage();
        $this->countStrategy = $configuration->getCountStrategy();

        // set query builder

        $criteria = Criteria::create()->orderBy($this->orderBy);
        $this->queryBuilder = $this->createQueryBuilder('e')->addCriteria($criteria);
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getClass(): string;

    protected function configure(BasicRepositoryConfiguration $configuration): void
    {
        // override this method to configure the repository
    }

    /**
     * @param null|non-empty-array<string,Order>|string $orderBy
     * @param int<1,max> $itemsPerPage
     */
    protected function with(
        null|EntityManagerInterface $entityManager = null,
        array|string|null $orderBy = null,
        ?int $itemsPerPage = null,
        ?CountStrategy $countStrategy = null,
    ): static {
        $clone = clone $this;
        $clone->entityManager = $entityManager ?? $this->entityManager;
        $clone->orderBy = OrderByUtil::normalizeOrderBy($orderBy ?? $this->orderBy);
        $clone->itemsPerPage = $itemsPerPage ?? $this->itemsPerPage;
        $clone->countStrategy = $countStrategy ?? $this->countStrategy;

        return $clone;
    }

    //
    // accessors for subclasses
    //

    final protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    final protected function createQueryBuilder(
        string $alias,
        ?string $indexBy = null
    ): QueryBuilder {
        return $this->getEntityManager()->createQueryBuilder()
            ->select($alias)
            ->from($this->getClass(), $alias, $indexBy);
    }

    //
    // interface methods
    //

    public function getReference(int|string $key): object
    {
        return $this->getEntityManager()
            ->getReference($this->getClass(), $key)
            ?? throw new NotFoundException('Entity not found');
    }

    public function contains(mixed $element): bool
    {
        if (!\is_object($element)) {
            return false;
        }

        return $this->getEntityManager()->contains($element);
    }

    public function containsKey(string|int $key): bool
    {
        return $this->get($key) !== null;
    }

    public function get(string|int $key): mixed
    {
        return $this->getEntityManager()->find($this->getClass(), $key);
    }

    public function getOrFail(string|int $key): mixed
    {
        return $this->get($key) ?? throw new NotFoundException('Entity not found');
    }

    public function add(mixed $element): void
    {
        $this->getEntityManager()->persist($element);
    }

    public function remove(mixed $element): void
    {
        $this->getEntityManager()->remove($element);
    }
}
