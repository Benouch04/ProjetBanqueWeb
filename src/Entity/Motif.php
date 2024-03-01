<?php

namespace App\Entity;

use App\Repository\MotifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotifRepository::class)]
class Motif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $libelleMotif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleMotif(): ?string
    {
        return $this->libelleMotif;
    }

    public function setLibelleMotif(string $libelleMotif): static
    {
        $this->libelleMotif = $libelleMotif;

        return $this;
    }
}
