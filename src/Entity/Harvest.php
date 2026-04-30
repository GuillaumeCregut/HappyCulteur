<?php

namespace App\Entity;

use App\Repository\HarvestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HarvestRepository::class)]
class Harvest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $weight = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Honey $honeyKind = null;

    #[ORM\ManyToOne(inversedBy: 'harvests')]
    private ?Hive $hive = null;

    #[ORM\ManyToOne(inversedBy: 'harvests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHoneyKind(): ?Honey
    {
        return $this->honeyKind;
    }

    public function setHoneyKind(?Honey $honeyKind): static
    {
        $this->honeyKind = $honeyKind;

        return $this;
    }

    public function getHive(): ?Hive
    {
        return $this->hive;
    }

    public function setHive(?Hive $hive): static
    {
        $this->hive = $hive;

        return $this;
    }

    public function getBeekeeper(): ?Apiculteur
    {
        return $this->beekeeper;
    }

    public function setBeekeeper(?Apiculteur $beekeeper): static
    {
        $this->beekeeper = $beekeeper;

        return $this;
    }
}
