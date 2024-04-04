<?php

namespace App\Entity;

use App\Repository\CompteClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteClientRepository::class)]
class CompteClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column]
    private ?int $montantDecouvert = null;

    #[ORM\ManyToOne(inversedBy: 'compteClients')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'compteClients')]
    private ?Compte $compte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTimeInterface $dateOuverture): static
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getMontantDecouvert(): ?int
    {
        return $this->montantDecouvert;
    }

    public function setMontantDecouvert(int $montantDecouvert): static
    {
        $this->montantDecouvert = $montantDecouvert;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): static
    {
        $this->compte = $compte;

        return $this;
    }
}
