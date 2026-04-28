<?php

namespace App\Entity;

use App\Constant\SwarmOrigin;
use App\Repository\SwarmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SwarmRepository::class)]
class Swarm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: 'integer', enumType: SwarmOrigin::class)]
    private ?SwarmOrigin $origin = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $specy = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $capturePlace = null;

    #[ORM\Column(nullable: true)]
    private ?int $QueenAge = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $QueenOrigin = null;

    #[ORM\OneToOne(inversedBy: 'swarm', cascade: ['persist', 'remove'])]
    private ?Hive $hive = null;

    #[ORM\ManyToOne(inversedBy: 'swarms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

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

    public function getOrigin(): ?SwarmOrigin
    {
        return $this->origin;
    }

    public function setOrigin(SwarmOrigin $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSpecy(): ?string
    {
        return $this->specy;
    }

    public function setSpecy(?string $specy): static
    {
        $this->specy = $specy;

        return $this;
    }

    public function getCapturePlace(): ?string
    {
        return $this->capturePlace;
    }

    public function setCapturePlace(?string $capturePlace): static
    {
        $this->capturePlace = $capturePlace;

        return $this;
    }

    public function getQueenAge(): ?int
    {
        return $this->QueenAge;
    }

    public function setQueenAge(?int $QueenAge): static
    {
        $this->QueenAge = $QueenAge;

        return $this;
    }

    public function getQueenOrigin(): ?string
    {
        return $this->QueenOrigin;
    }

    public function setQueenOrigin(?string $QueenOrigin): static
    {
        $this->QueenOrigin = $QueenOrigin;

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
