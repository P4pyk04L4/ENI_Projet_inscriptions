<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profils', name: 'profils_')]
class ProfilsController extends AbstractController
{
    #[Route('/monprofil', name: 'monProfil')]
    public function index(): Response
    {
        return $this->render('profile/monProfil.html.twig');
    }

    #[Route('/monprofil/modifier', name: 'monProfil_modifier')]
    public function modifier(ParticipantRepository $participantRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Participant $participant */
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $entityManager->flush();

            $this->addFlash(
                'success', 'Votre profil a bien été modifié.'
            );

            return $this->redirectToRoute('profils_monProfil');
        }

        return $this->render('profile/modifier.html.twig', [
            'participant' => $participantForm->createView()
        ]);
    }
}
