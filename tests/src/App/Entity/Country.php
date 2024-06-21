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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Rekalogika\Collections\Tests\App\DoctrineRepository\DoctrineCountryRepository;

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
    #[ORM\OneToMany(targetEntity: Citizen::class, mappedBy: 'country', orphanRemoval: true)]
    private Collection $citizens;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function __construct()
    {
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
    public function getCitizens(): Collection
    {
        return $this->citizens;
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
