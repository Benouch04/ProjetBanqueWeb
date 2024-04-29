<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomContrat = null;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: ContratClient::class)]
    private Collection $contratClients;

    public function __construct()
    {
        $this->contratClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomContrat(): ?string
    {
        return $this->nomContrat;
    }

    public function setNomContrat(string $nomContrat): static
    {
        $this->nomContrat = $nomContrat;

        return $this;
    }
    public function __toString()
    {
        return $this->nomContrat; // ou tout autre propriété qui doit être convertie en chaîne
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
            $contratClient->setContrat($this);
        }

        return $this;
    }

    public function removeContratClient(ContratClient $contratClient): static
    {
        if ($this->contratClients->removeElement($contratClient)) {
            // set the owning side to null (unless already changed)
            if ($contratClient->getContrat() === $this) {
                $contratClient->setContrat(null);
            }
        }

        return $this;
    }
}
