<?php

namespace App\Entity\Archive;

use App\Entity\Apiculteur;
use App\Repository\Archive\VisitsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitsRepository::class)]
#[ORM\Table(name: 'archive_visit')]
class Visit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $hive = null;

    #[ORM\Column(length: 100)]
    private ?string $weather = null;

    #[ORM\Column]
    private ?bool $isDisease = null;

    #[ORM\Column(nullable: true)]
    private ?float $temperature = null;

    #[ORM\Column(nullable: true)]
    private ?int $hygrometry = null;

    #[ORM\Column]
    private ?bool $isWorkToDo = null;

    #[ORM\Column]
    private ?bool $isFeeded = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $feeding = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column]
    private ?bool $isQueenVisible = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $population = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $behaviour = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $disease = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;

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

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(string $weather): static
    {
        $this->weather = $weather;

        return $this;
    }

    public function isDisease(): ?bool
    {
        return $this->isDisease;
    }

    public function setIsDisease(bool $isDisease): static
    {
        $this->isDisease = $isDisease;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getHygrometry(): ?int
    {
        return $this->hygrometry;
    }

    public function setHygrometry(?int $hygrometry): static
    {
        $this->hygrometry = $hygrometry;

        return $this;
    }

    public function isWorkToDo(): ?bool
    {
        return $this->isWorkToDo;
    }

    public function setIsWorkToDo(bool $isWorkToDo): static
    {
        $this->isWorkToDo = $isWorkToDo;

        return $this;
    }

    public function isFeeded(): ?bool
    {
        return $this->isFeeded;
    }

    public function setIsFeeded(bool $isFeeded): static
    {
        $this->isFeeded = $isFeeded;

        return $this;
    }

    public function getFeeding(): ?string
    {
        return $this->feeding;
    }

    public function setFeeding(?string $feeding): static
    {
        $this->feeding = $feeding;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function isQueenVisible(): ?bool
    {
        return $this->isQueenVisible;
    }

    public function setIsQueenVisible(bool $isQueenVisible): static
    {
        $this->isQueenVisible = $isQueenVisible;

        return $this;
    }

    public function getPopulation(): ?string
    {
        return $this->population;
    }

    public function setPopulation(?string $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getBehaviour(): ?string
    {
        return $this->behaviour;
    }

    public function setBehaviour(?string $behaviour): static
    {
        $this->behaviour = $behaviour;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDisease(): ?string
    {
        return $this->disease;
    }

    public function setDisease(?string $disease): static
    {
        $this->disease = $disease;

        return $this;
    }
}
