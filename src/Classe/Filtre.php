<?php

namespace App\Classe;

use App\Entity\Campus;
use phpDocumentor\Reflection\Types\Boolean;

class Filtre
{
    private ?Campus $campus = null;
    private ?string $nomSortie = null;
    private ?\DateTimeInterface $dateDebut = null;
    private ?\DateTimeInterface $dateFin = null;
    private bool|null $estOrganisateur = null;
    private bool|null $estInscrit = null;
    private bool|null $nEstPasInscrit = null;
    private bool|null $sortiesPassees = null;

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(?string $nomSortie): void
    {
        $this->nomSortie = $nomSortie;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getEstOrganisateur(): ?bool
    {
        return $this->estOrganisateur;
    }

    public function setEstOrganisateur(?bool $estOrganisateur): void
    {
        $this->estOrganisateur = $estOrganisateur;
    }

    public function getEstInscrit(): ?bool
    {
        return $this->estInscrit;
    }

    public function setEstInscrit(?bool $estInscrit): void
    {
        $this->estInscrit = $estInscrit;
    }

    public function getNEstPasInscrit(): ?bool
    {
        return $this->nEstPasInscrit;
    }

    public function setNEstPasInscrit(?bool $nEstPasInscrit): void
    {
        $this->nEstPasInscrit = $nEstPasInscrit;
    }

    public function getSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }

    public function setSortiesPassees(?bool $sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }




}