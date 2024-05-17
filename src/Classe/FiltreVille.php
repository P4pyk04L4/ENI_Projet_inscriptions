<?php

namespace App\Classe;

class FiltreVille
{
    private ?string $nomVille = null;

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(?string $nomVille): void
    {
        $this->nomVille = $nomVille;
    }

}