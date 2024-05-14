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
            $filtreRecherche = $filtreForm->getData();
            dump($filtreRecherche);
        }

        return $this->render('sorties/index.html.twig', [
            'controller_name' => 'SortiesController',
            'sorties' => $sorties,
            'filtreForm' =>$filtreForm->createView()
        ]);
    }
}
