<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomCompte = null;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: Operation::class)]
    private Collection $CompteOpe;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: CompteClient::class)]
    private Collection $compteClients;

    public function __construct()
    {
        $this->CompteOpe = new ArrayCollection();
        $this->compteClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCompte(): ?string
    {
        return $this->NomCompte;
    }

    public function setNomCompte(string $NomCompte): static
    {
        $this->NomCompte = $NomCompte;

        return $this;
    }
    public function __toString()
    {
        return $this->NomCompte; // ou tout autre propriété qui doit être convertie en chaîne
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getCompteOpe(): Collection
    {
        return $this->CompteOpe;
    }

    public function addCompteOpe(Operation $compteOpe): static
    {
        if (!$this->CompteOpe->contains($compteOpe)) {
            $this->CompteOpe->add($compteOpe);
            $compteOpe->setCompte($this);
        }

        return $this;
    }

    public function removeCompteOpe(Operation $compteOpe): static
    {
        if ($this->CompteOpe->removeElement($compteOpe)) {
            // set the owning side to null (unless already changed)
            if ($compteOpe->getCompte() === $this) {
                $compteOpe->setCompte(null);
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
            $compteClient->setCompte($this);
        }

        return $this;
    }

    public function removeCompteClient(CompteClient $compteClient): static
    {
        if ($this->compteClients->removeElement($compteClient)) {
            // set the owning side to null (unless already changed)
            if ($compteClient->getCompte() === $this) {
                $compteClient->setCompte(null);
            }
        }

        return $this;
    }
}
