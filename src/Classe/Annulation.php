<?php

namespace App\Classe;

class Annulation
{

    private ?string $motifAnnulation = null;

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): void
    {
        $this->motifAnnulation = $motifAnnulation;
    }

}