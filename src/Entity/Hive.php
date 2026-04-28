<?php

namespace App\Entity;

use App\Constant\HiveState;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HiveRepository;

#[ORM\Entity(repositoryClass: HiveRepository::class)]
class Hive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $frameNumber = null;

    #[ORM\Column]
    private ?int $riseNumber = null;

    #[ORM\Column(length: 30)]
    private ?string $identification = null;

    #[ORM\Column(type: 'integer', enumType: HiveState::class)]
    private ?HiveState $state = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qrCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $coordX = null;

    #[ORM\Column(nullable: true)]
    private ?int $coordY = null;

    #[ORM\Column(nullable: true)]
    private ?int $coordZ = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiary $apiary = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HiveKind $kind = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    private ?HiveRise $rise = null;

    #[ORM\OneToOne(mappedBy: 'hive', cascade: ['persist', 'remove'])]
    private ?Swarm $swarm = null;

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

    public function getFrameNumber(): ?int
    {
        return $this->frameNumber;
    }

    public function setFrameNumber(int $frameNumber): static
    {
        $this->frameNumber = $frameNumber;

        return $this;
    }

    public function getRiseNumber(): ?int
    {
        return $this->riseNumber;
    }

    public function setRiseNumber(int $riseNumber): static
    {
        $this->riseNumber = $riseNumber;

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

    public function getState(): ?HiveState
    {
        return $this->state;
    }

    public function setState(HiveState $state): static
    {
        $this->state = $state;

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

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(?string $qrCode): static
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function getCoordX(): ?int
    {
        return $this->coordX;
    }

    public function setCoordX(?int $coordX): static
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coordY;
    }

    public function setCoordY(?int $coordY): static
    {
        $this->coordY = $coordY;

        return $this;
    }

    public function getCoordZ(): ?int
    {
        return $this->coordZ;
    }

    public function setCoordZ(?int $coordZ): static
    {
        $this->coordZ = $coordZ;

        return $this;
    }

    public function getApiary(): ?Apiary
    {
        return $this->apiary;
    }

    public function setApiary(?Apiary $apiary): static
    {
        $this->apiary = $apiary;

        return $this;
    }

    public function getKind(): ?HiveKind
    {
        return $this->kind;
    }

    public function setKind(?HiveKind $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    public function getRise(): ?HiveRise
    {
        return $this->rise;
    }

    public function setRise(?HiveRise $rise): static
    {
        $this->rise = $rise;

        return $this;
    }

    public function getSwarm(): ?Swarm
    {
        return $this->swarm;
    }

    public function setSwarm(?Swarm $swarm): static
    {
        // unset the owning side of the relation if necessary
        if ($swarm === null && $this->swarm !== null) {
            $this->swarm->setHive(null);
        }

        // set the owning side of the relation if necessary
        if ($swarm !== null && $swarm->getHive() !== $this) {
            $swarm->setHive($this);
        }

        $this->swarm = $swarm;

        return $this;
    }
}
