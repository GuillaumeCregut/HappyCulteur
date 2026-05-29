<?php

namespace App\Entity;

use App\Repository\ApiaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiaryRepository::class)]
class Apiary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $localisation = null;

    #[ORM\Column(length: 15)]
    private ?string $identification = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $gps = null;

    #[ORM\Column(length: 130, nullable: true)]
    private ?string $pathImage = null;

    #[ORM\Column(length: 130, nullable: true)]
    private ?string $lastPicture = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\ManyToOne(inversedBy: 'apiaries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $owner = null;

    /**
     * @var Collection<int, Hive>
     */
    #[ORM\OneToMany(targetEntity: Hive::class, mappedBy: 'apiary')]
    private Collection $hives;

    /**
     * @var Collection<int, Apiculteur>
     */
    #[ORM\ManyToMany(targetEntity: Apiculteur::class, inversedBy: 'multiApiaries')]
    private Collection $beekeepers;

    public function __construct()
    {
        $this->hives = new ArrayCollection();
        $this->beekeepers = new ArrayCollection();
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

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(string $identification): static
    {
        $this->identification = $identification;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getGps(): ?string
    {
        return $this->gps;
    }

    public function setGps(?string $gps): static
    {
        $this->gps = $gps;

        return $this;
    }

    public function getPathImage(): ?string
    {
        return $this->pathImage;
    }

    public function setPathImage(string $pathImage): static
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    public function getLastPicture(): ?string
    {
        return $this->lastPicture;
    }

    public function setLastPicture(?string $lastPicture): static
    {
        $this->lastPicture = $lastPicture;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    public function getOwner(): ?Apiculteur
    {
        return $this->owner;
    }

    public function setOwner(?Apiculteur $beekeeper): static
    {
        $this->owner = $beekeeper;

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
            $hive->setApiary($this);
        }

        return $this;
    }

    public function removeHive(Hive $hive): static
    {
        if ($this->hives->removeElement($hive)) {
            // set the owning side to null (unless already changed)
            if ($hive->getApiary() === $this) {
                $hive->setApiary(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Apiculteur>
     */
    public function getBeekeepers(): Collection
    {
        return $this->beekeepers;
    }

    public function addBeekeeper(Apiculteur $beekeeper): static
    {
        if (!$this->beekeepers->contains($beekeeper)) {
            $this->beekeepers->add($beekeeper);
        }

        return $this;
    }

    public function removeBeekeeper(Apiculteur $beekeeper): static
    {
        $this->beekeepers->removeElement($beekeeper);

        return $this;
    }
}
