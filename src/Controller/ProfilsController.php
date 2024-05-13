<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profils', name: 'profils_')]
class ProfilsController extends AbstractController
{
    #[Route('/monprofil', name: 'monProfil')]
    public function index(): Response
    {
        return $this->render('profile/monProfil.html.twig');
    }
}
