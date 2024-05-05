<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomClient = null;

    #[ORM\Column(length: 255)]
    private ?string $prenomClient = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseClient = null;

    #[ORM\Column]
    private ?int $numTel = null;

    #[ORM\Column(length: 255)]
    private ?string $situation = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Users $parent = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Operation::class)]
    private Collection $ClientOpe;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CompteClient::class)]
    private Collection $compteClients;

    #[ORM\OneToMany(mappedBy: 'clients', targetEntity: Calendar::class)]
    private Collection $calendars;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ContratClient::class)]
    private Collection $contratClients;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    public function __construct()
    {
        $this->ClientOpe = new ArrayCollection();
        $this->compteClients = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->contratClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFullName(): string
    {

        return $this->nomClient . ' ' . $this->prenomClient;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): static
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getPrenomClient(): ?string
    {
        return $this->prenomClient;
    }

    public function setPrenomClient(string $prenomClient): static
    {
        $this->prenomClient = $prenomClient;

        return $this;
    }

    public function getAdresseClient(): ?string
    {
        return $this->adresseClient;
    }

    public function setAdresseClient(string $adresseClient): static
    {
        $this->adresseClient = $adresseClient;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): static
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): static
    {
        $this->situation = $situation;

        return $this;
    }

    public function getParent(): ?Users
    {
        return $this->parent;
    }

    public function setParent(?Users $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getClientOpe(): Collection
    {
        return $this->ClientOpe;
    }

    public function addClientOpe(Operation $clientOpe): static
    {
        if (!$this->ClientOpe->contains($clientOpe)) {
            $this->ClientOpe->add($clientOpe);
            $clientOpe->setClient($this);
        }

        return $this;
    }

    public function removeClientOpe(Operation $clientOpe): static
    {
        if ($this->ClientOpe->removeElement($clientOpe)) {
            // set the owning side to null (unless already changed)
            if ($clientOpe->getClient() === $this) {
                $clientOpe->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompteClient>
     */
    public function getCompteClients(): Collection
    {
        return $this->compteClients;
    }

    public function addCompteClient(CompteClient $compteClient): static
    {
        if (!$this->compteClients->contains($compteClient)) {
            $this->compteClients->add($compteClient);
            $compteClient->setClient($this);
        }

        return $this;
    }

    public function removeCompteClient(CompteClient $compteClient): static
    {
        if ($this->compteClients->removeElement($compteClient)) {
            // set the owning side to null (unless already changed)
            if ($compteClient->getClient() === $this) {
                $compteClient->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->setClients($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getClients() === $this) {
                $calendar->setClients(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nomClient; // ou toute autre propriété qui représente un objet Client en tant que chaîne
    }

    /**
     * @return Collection<int, ContratClient>
     */
    public function getContratClients(): Collection
    {
        return $this->contratClients;
    }

    public function addContratClient(ContratClient $contratClient): static
    {
        if (!$this->contratClients->contains($contratClient)) {
            $this->contratClients->add($contratClient);
            $contratClient->setClient($this);
        }

        return $this;
    }

    public function removeContratClient(ContratClient $contratClient): static
    {
        if ($this->contratClients->removeElement($contratClient)) {
            // set the owning side to null (unless already changed)
            if ($contratClient->getClient() === $this) {
                $contratClient->setClient(null);
            }
        }

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(?\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    
}
