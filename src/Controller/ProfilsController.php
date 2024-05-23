<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\PhotoProfil;
use App\Form\ParticipantType;
use App\Form\PhotoProfilType;
use App\Repository\PhotoProfilRepository;
use App\Service\FileUploader;
use App\Service\SortiesChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profils', name: 'profils_')]
class ProfilsController extends AbstractController
{
    #[Route('/monprofil', name: 'monProfil')]
    public function index(): Response
    {
        return $this->render('profile/monProfil.html.twig');
    }

    #[Route('/monprofil/modifier', name: 'monProfil_modifier')]
    public function modifier(Request $request, EntityManagerInterface $entityManager,
                             FileUploader $fileUploader): Response
    {
        /** @var Participant $participant */
        $participant = $this->getUser();
        $participantForm = $this->createForm(ParticipantType::class, $participant);
        $participantForm->handleRequest($request);

        $newPhoto = new PhotoProfil();
        $newPhoto->setDateUpload(new \DateTime('now'))
            ->setParticipant($this->getUser())
            ->setPhotoActive(true);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $fichierPhoto = $participantForm->get('photoProfil')->getData();
            if($fichierPhoto){
                $photoCheck = $fileUploader->checkAndUpload($fichierPhoto);
                $newPhoto->setNom($photoCheck->getNom())
                    ->setCheminAcces($photoCheck->getCheminAcces());
                $entityManager->persist($newPhoto);
            }

            $entityManager->flush();

            $this->addFlash(
                'success', 'Votre profil a bien été modifié.'
            );

            return $this->redirectToRoute('profils_monProfil');
        }

        return $this->render('profile/modifier.html.twig', [
            'participant' => $participantForm->createView(),
        ]);
    }

    #[Route('/profil/{id}', name: 'afficher_participant', requirements: ['id' => '\d+'])]
    public function afficherProfilParticipant(Participant $participant): Response
    {
        return $this->render('profile/profilParticipant.html.twig', [
            'participant' => $participant
        ]);
    }
}
