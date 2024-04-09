<?php

namespace App\Entity;

use App\Repository\ContratRepository;
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
}
