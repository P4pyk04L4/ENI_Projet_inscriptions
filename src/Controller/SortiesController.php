<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'sortie_')]
class SortiesController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sorties/index.html.twig', [
            'controller_name' => 'SortiesController',
            'sorties' => $sorties
        ]);
    }
}
