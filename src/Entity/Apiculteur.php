<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ApiculteurRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ApiculteurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[UniqueEntity(fields: ['login'], message: 'There is already an account with this login')]
class Apiculteur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $login = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $codeAPE = null;

    #[ORM\Column(length: 10)]
    private ?string $codeAPI = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 30)]
    private ?string $numagri = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 150)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 10)]
    #[Assert\Length(max: 10, maxMessage:'Ne pas dépasser 10 caractères')]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 5)]
    private ?string $zipCode = null;

    /**
     * @var Collection<int, Apiary>
     */
    #[ORM\OneToMany(targetEntity: Apiary::class, mappedBy: 'beekeeper')]
    private Collection $apiaries;

    /**
     * @var Collection<int, Purchase>
     */
    #[ORM\OneToMany(targetEntity: Purchase::class, mappedBy: 'beekeeper', orphanRemoval: true)]
    private Collection $purchases;

    /**
     * @var Collection<int, Swarm>
     */
    #[ORM\OneToMany(targetEntity: Swarm::class, mappedBy: 'beekeeper')]
    private Collection $swarms;

    public function __construct()
    {
        $this->apiaries = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->swarms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getCodeAPE(): ?string
    {
        return $this->codeAPE;
    }

    public function setCodeAPE(?string $codeAPE): static
    {
        $this->codeAPE = $codeAPE;

        return $this;
    }

    public function getCodeAPI(): ?string
    {
        return $this->codeAPI;
    }

    public function setCodeAPI(string $codeAPI): static
    {
        $this->codeAPI = $codeAPI;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getNumagri(): ?string
    {
        return $this->numagri;
    }

    public function setNumagri(string $numagri): static
    {
        $this->numagri = $numagri;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return Collection<int, Apiary>
     */
    public function getApiaries(): Collection
    {
        return $this->apiaries;
    }

    public function addApiary(Apiary $apiary): static
    {
        if (!$this->apiaries->contains($apiary)) {
            $this->apiaries->add($apiary);
            $apiary->setBeekeeper($this);
        }

        return $this;
    }

    public function removeApiary(Apiary $apiary): static
    {
        if ($this->apiaries->removeElement($apiary)) {
            // set the owning side to null (unless already changed)
            if ($apiary->getBeekeeper() === $this) {
                $apiary->setBeekeeper(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setBeekeeper($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getBeekeeper() === $this) {
                $purchase->setBeekeeper(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Swarm>
     */
    public function getSwarms(): Collection
    {
        return $this->swarms;
    }

    public function addSwarm(Swarm $swarm): static
    {
        if (!$this->swarms->contains($swarm)) {
            $this->swarms->add($swarm);
            $swarm->setBeekeeper($this);
        }

        return $this;
    }

    public function removeSwarm(Swarm $swarm): static
    {
        if ($this->swarms->removeElement($swarm)) {
            // set the owning side to null (unless already changed)
            if ($swarm->getBeekeeper() === $this) {
                $swarm->setBeekeeper(null);
            }
        }

        return $this;
    }
}
