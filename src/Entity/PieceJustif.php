<?php

namespace App\Entity;

use App\Repository\PieceJustifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: Motif::class, mappedBy: 'motifPj')]
    private Collection $motifs;

    public function __construct()
    {
        $this->motifs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Motif>
     */
    public function getMotifs(): Collection
    {
        return $this->motifs;
    }

    public function addMotif(Motif $motif): static
    {
        if (!$this->motifs->contains($motif)) {
            $this->motifs->add($motif);
            $motif->addMotifPj($this);
        }

        return $this;
    }

    public function removeMotif(Motif $motif): static
    {
        if ($this->motifs->removeElement($motif)) {
            $motif->removeMotifPj($this);
        }

        return $this;
    }
}
