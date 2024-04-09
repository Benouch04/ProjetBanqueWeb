<?php

namespace App\Entity;

use App\Repository\MotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'motif_id', targetEntity: Calendar::class)]
    private Collection $calendars;

    #[ORM\ManyToMany(targetEntity: PieceJustif::class, inversedBy: 'motifs')]
    private Collection $motifPj;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
        $this->motifPj = new ArrayCollection();
    }

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
            $calendar->setMotif($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getMotif() === $this) {
                $calendar->setMotif(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PieceJustif>
     */
    public function getMotifPj(): Collection
    {
        return $this->motifPj;
    }

    public function addMotifPj(PieceJustif $motifPj): static
    {
        if (!$this->motifPj->contains($motifPj)) {
            $this->motifPj->add($motifPj);
        }

        return $this;
    }

    public function removeMotifPj(PieceJustif $motifPj): static
    {
        $this->motifPj->removeElement($motifPj);

        return $this;
    }
}
