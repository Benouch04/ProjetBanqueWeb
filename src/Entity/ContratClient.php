<?php

namespace App\Entity;

use App\Repository\ContratClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratClientRepository::class)]
class ContratClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOuvertureContrat = null;

    #[ORM\Column]
    private ?int $tarifMensuel = null;

    #[ORM\ManyToOne(inversedBy: 'contratClients')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'contratClients')]
    private ?Contrat $contrat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOuvertureContrat(): ?\DateTimeInterface
    {
        return $this->dateOuvertureContrat;
    }

    public function setDateOuvertureContrat(\DateTimeInterface $dateOuvertureContrat): static
    {
        $this->dateOuvertureContrat = $dateOuvertureContrat;

        return $this;
    }

    public function getTarifMensuel(): ?int
    {
        return $this->tarifMensuel;
    }

    public function setTarifMensuel(int $tarifMensuel): static
    {
        $this->tarifMensuel = $tarifMensuel;

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

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }
}
