<?php

namespace App\Service;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortiesChecker
{
    private EtatRepository $etatRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(EtatRepository $etatRepository, EntityManagerInterface $entityManager){
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }

    public function checkSorties(Sortie $sortie): Sortie
    {
        $now = new \DateTime();
        # Vérifier l'Etat de la sortie
        if(($sortie->getDateLimiteInscription() <= $now || $sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) && $sortie->getEtat()->getLibelle() === 'Ouverte'){
            $etatCloture = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
            $sortie->setEtat($etatCloture);
        }

        if($sortie->getDateHeureDebut() <= $now && $sortie->getEtat()->getLibelle() === 'Ouverte'){
            $etatEnCours = $this->etatRepository->findOneBy(['libelle' => 'Activité en cours']);
            $sortie->setEtat($etatEnCours);
        }

    if($sortie->getDateHeureDebut()->modify('+'.($sortie->getDuree()*60). ' minutes') <= $now && in_array($sortie->getEtat()->getLibelle(), ['Activité en cours', 'Clôturée'])){
            $etatPasse = $this->etatRepository->findOneBy(['libelle' => 'Activité passée']);
            $sortie->setEtat($etatPasse);
        }
        $this->entityManager->flush();

        return $sortie;
    }
}