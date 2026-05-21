<?php

namespace App\Entity;

use App\Constant\HiveState;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?Apiary $apiary = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HiveKind $kind = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    private ?HiveRise $rise = null;

    #[ORM\OneToOne(mappedBy: 'hive', cascade: ['persist'])]
    private ?Swarm $swarm = null;

    /**
     * @var Collection<int, Visit>
     */
    #[ORM\OneToMany(targetEntity: Visit::class, mappedBy: 'hive')]
    private Collection $visits;

    /**
     * @var Collection<int, Harvest>
     */
    #[ORM\OneToMany(targetEntity: Harvest::class, mappedBy: 'hive')]
    private Collection $harvests;

    #[ORM\OneToMany(targetEntity: Datalogger::class, mappedBy: 'hive')]
    private Collection $datalogger;

    #[ORM\Column(nullable: true)]
    private ?string $dataLoggerName = null;

    #[ORM\ManyToOne(inversedBy: 'hives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apiculteur $beekeeper = null;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
        $this->harvests = new ArrayCollection();
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

    /**
     * @return Collection<int, Visit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): static
    {
        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
            $visit->setHive($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): static
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getHive() === $this) {
                $visit->setHive(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Harvest>
     */
    public function getHarvests(): Collection
    {
        return $this->harvests;
    }

    public function addHarvest(Harvest $harvest): static
    {
        if (!$this->harvests->contains($harvest)) {
            $this->harvests->add($harvest);
            $harvest->setHive($this);
        }

        return $this;
    }

    public function removeHarvest(Harvest $harvest): static
    {
        if ($this->harvests->removeElement($harvest)) {
            // set the owning side to null (unless already changed)
            if ($harvest->getHive() === $this) {
                $harvest->setHive(null);
            }
        }

        return $this;
    }

    public function getDatalogger(): ?Collection
    {
        return $this->datalogger;
    }

    public function addDatalogger(Datalogger $datalogger): static
    {
        // set the owning side of the relation if necessary
        if (!$this->datalogger->contains($datalogger)) {
            $this->datalogger->add($datalogger);
            $datalogger->setHive($this);
        }

        return $this;
    }

    public function removeDatalogger(Datalogger $datalogger): static
    {
        if ($this->datalogger->removeElement($datalogger)) {
            // set the owning side to null (unless already changed)
            if ($datalogger->getHive() === $this) {
                $datalogger->setHive(null);
            }
        }

        return $this;
    }


    public function getDataloggerName(): ?string
    {
        return $this->dataLoggerName;
    }

    public function setDataloggerName(?string $dataLoggerName): static
    {
        $this->dataLoggerName = $dataLoggerName;

        return $this;
    }

    public function getOwner(): ?Apiculteur
    {
        return $this->beekeeper;
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
