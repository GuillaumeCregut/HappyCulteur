<?php

namespace App\Entity\Archive;

use App\Entity\Apiculteur;
use App\Repository\Archive\HarvestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HarvestRepository::class)]
#[ORM\Table(name: 'archive_harvest')]
class Harvest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $weight = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $honeyKind = null;

    #[ORM\Column(length: 255)]
    private ?string $hive = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

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

    public function getHoneyKind(): ?string
    {
        return $this->honeyKind;
    }

    public function setHoneyKind(string $honeyKind): static
    {
        $this->honeyKind = $honeyKind;

        return $this;
    }

    public function getHive(): ?string
    {
        return $this->hive;
    }

    public function setHive(string $hive): static
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}
