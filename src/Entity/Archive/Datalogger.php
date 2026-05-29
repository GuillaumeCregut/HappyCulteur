<?php

namespace App\Entity\Archive;

use App\Entity\Apiculteur;
use App\Repository\Archive\DataloggerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataloggerRepository::class)]
#[ORM\Table(name: 'archive_datalogger')]
class Datalogger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $identification = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateTime = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(nullable: true)]
    private ?float $extTemp = null;

    #[ORM\Column(nullable: true)]
    private ?float $intTemp = null;

    #[ORM\Column(nullable: true)]
    private ?int $intHygro = null;

    #[ORM\Column(nullable: true)]
    private ?int $extHygro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hive = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeImmutable $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
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

    public function getExtTemp(): ?float
    {
        return $this->extTemp;
    }

    public function setExtTemp(?float $extTemp): static
    {
        $this->extTemp = $extTemp;

        return $this;
    }

    public function getIntTemp(): ?float
    {
        return $this->intTemp;
    }

    public function setIntTemp(?float $intTemp): static
    {
        $this->intTemp = $intTemp;

        return $this;
    }

    public function getIntHygro(): ?int
    {
        return $this->intHygro;
    }

    public function setIntHygro(?int $intHygro): static
    {
        $this->intHygro = $intHygro;

        return $this;
    }

    public function getExtHygro(): ?int
    {
        return $this->extHygro;
    }

    public function setExtHygro(?int $extHygro): static
    {
        $this->extHygro = $extHygro;

        return $this;
    }

    public function getHive(): ?string
    {
        return $this->hive;
    }

    public function setHive(?string $hive): static
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
