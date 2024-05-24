<?php

namespace App\Entity;

class FiltreCampus
{
    private ?string $nomCampus = null;

    public function getNomCampus(): ?string
    {
        return $this->nomCampus;
    }

    public function setNomCampus(?string $nomCampus): void
    {
        $this->nomCampus = $nomCampus;
    }


}