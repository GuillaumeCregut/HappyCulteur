<?php

namespace App\Entity;

use App\Repository\HiveRiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HiveRiseRepository::class)]
class HiveRise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Hive>
     */
    #[ORM\OneToMany(targetEntity: Hive::class, mappedBy: 'rise')]
    private Collection $hives;

    public function __construct()
    {
        $this->hives = new ArrayCollection();
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
     * @return Collection<int, Hive>
     */
    public function getHives(): Collection
    {
        return $this->hives;
    }

    public function addHive(Hive $hive): static
    {
        if (!$this->hives->contains($hive)) {
            $this->hives->add($hive);
            $hive->setRise($this);
        }

        return $this;
    }

    public function removeHive(Hive $hive): static
    {
        if ($this->hives->removeElement($hive)) {
            // set the owning side to null (unless already changed)
            if ($hive->getRise() === $this) {
                $hive->setRise(null);
            }
        }

        return $this;
    }
}
