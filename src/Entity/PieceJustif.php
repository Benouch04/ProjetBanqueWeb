<?php

namespace App\Entity;

use App\Repository\PieceJustifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PieceJustifRepository::class)]
class PieceJustif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomPieceJustif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPieceJustif(): ?string
    {
        return $this->nomPieceJustif;
    }

    public function setNomPieceJustif(string $nomPieceJustif): static
    {
        $this->nomPieceJustif = $nomPieceJustif;

        return $this;
    }
}
