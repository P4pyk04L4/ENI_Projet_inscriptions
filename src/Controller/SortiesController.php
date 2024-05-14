<?php

namespace App\Controller;

use App\Classe\Filtre;
use App\Form\FiltreType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'sortie_')]
class SortiesController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        $sorties = $sortieRepository->findAllSorties();

        $filtre = new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        $filtreForm->handleRequest($request);

        if($filtreForm->isSubmitted() && $filtreForm->isValid()){

            $campus = $filtreForm->get('campus')->getData();
            $nomSortie = $filtreForm->get('nomSortie')->getData();
            $dateDebut = $filtreForm->get('dateDebut')->getData();
            $dateFin = $filtreForm->get('dateFin')->getData();
            $estOrganisateur = $filtreForm->get('estOrganisateur')->getData();
            $estInscrit = $filtreForm->get('estInscrit')->getData();
            $nEstPasInscrit = $filtreForm->get('nEstPasInscrit')->getData();
            $sortiesPassees = $filtreForm->get('sortiesPassees')->getData();

            $user = $this->getUser();

            $resultats = $sortieRepository->afficherSortiesFiltrees($user, $campus, $nomSortie, $dateDebut, $dateFin, $estOrganisateur, $estInscrit, $nEstPasInscrit, $sortiesPassees);
            dump($resultats);

            return $this->render('sorties/index.html.twig', [
                'controller_name' => 'SortiesController',
                'sorties' => $resultats,
                'filtreForm' =>$filtreForm->createView()
            ]);
        }

        return $this->render('sorties/index.html.twig', [
            'controller_name' => 'SortiesController',
            'sorties' => $sorties,
            'filtreForm' =>$filtreForm->createView()
        ]);
    }
}
