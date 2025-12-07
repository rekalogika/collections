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

namespace Rekalogika\Collections\Tests\App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Rekalogika\Collections\Tests\App\DoctrineRepository\DoctrineCountryRepository;
use Rekalogika\Contracts\Collections\MinimalReadableRecollection;
use Rekalogika\Contracts\Collections\MinimalRecollection;
use Rekalogika\Contracts\Collections\ReadableRecollection;
use Rekalogika\Contracts\Collections\Recollection;
use Rekalogika\Domain\Collections\ArrayCollection;
use Rekalogika\Domain\Collections\Common\Count\DisabledCountStrategy;
use Rekalogika\Domain\Collections\Common\Pagination;
use Rekalogika\Domain\Collections\CriteriaRecollection;
use Rekalogika\Domain\Collections\MinimalCriteriaRecollection;
use Rekalogika\Domain\Collections\MinimalRecollectionDecorator;
use Rekalogika\Domain\Collections\RecollectionDecorator;

#[ORM\Entity(repositoryClass: DoctrineCountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Citizen>
     */
    #[ORM\OneToMany(targetEntity: Citizen::class, mappedBy: 'country', orphanRemoval: true, indexBy: 'id')]
    private Collection $citizens;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function __construct()
    {
        /** @psalm-suppress DeprecatedClass */
        $this->citizens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Citizen>
     */
    public function getRawCitizens(): Collection
    {
        return $this->citizens;
    }

    private function getWorkingAgeCriteria(): Criteria
    {
        return Criteria::create(true)
            ->where(Criteria::expr()->gte('age', 15))
            ->andWhere(Criteria::expr()->lte('age', 64));
    }

    /**
     * @return Recollection<int, Citizen>
     */
    public function getCitizensInRecollection(
        Pagination $pagination,
    ): Recollection {
        return RecollectionDecorator::create(
            collection: $this->citizens,
            indexBy: 'id',
            pagination: $pagination,
            count: new DisabledCountStrategy(),
        );
    }

    /**
     * @return MinimalRecollection<int, Citizen>
     */
    public function getCitizensInMinimalRecollection(
        Pagination $pagination,
    ): MinimalRecollection {
        return MinimalRecollectionDecorator::create(
            collection: $this->citizens,
            indexBy: 'id',
            pagination: $pagination,
            count: new DisabledCountStrategy(),
        );
    }

    /**
     * @return ReadableRecollection<int, Citizen>
     */
    public function getWorkingAgeCitizensInRecollection(
        Pagination $pagination,
    ): ReadableRecollection {
        return CriteriaRecollection::create(
            collection: $this->citizens,
            criteria: $this->getWorkingAgeCriteria(),
            indexBy: 'id',
            instanceId: __METHOD__,
            pagination: $pagination,
        );
    }

    /**
     * @return MinimalReadableRecollection<int, Citizen>
     */
    public function getWorkingAgeCitizensInMinimalRecollection(
        Pagination $pagination,
    ): MinimalReadableRecollection {
        return MinimalCriteriaRecollection::create(
            collection: $this->citizens,
            criteria: $this->getWorkingAgeCriteria(),
            indexBy: 'id',
            instanceId: __METHOD__,
            pagination: $pagination,
        );
    }

    public function addCitizen(Citizen $citizen): static
    {
        if (!$this->citizens->contains($citizen)) {
            $this->citizens->add($citizen);
            $citizen->setCountry($this);
        }

        return $this;
    }

    public function removeCitizen(Citizen $citizen): static
    {
        if ($this->citizens->removeElement($citizen)) {
            // set the owning side to null (unless already changed)
            if ($citizen->getCountry() === $this) {
                $citizen->setCountry(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
