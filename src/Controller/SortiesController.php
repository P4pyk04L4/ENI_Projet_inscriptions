<?php

namespace App\Controller;

use App\Classe\Filtre;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'sortie_')]
class SortiesController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(SortieRepository $sortieRepository,ParticipantRepository $participantRepository, Request $request): Response
    {
        $sorties = $sortieRepository->findAllActiveSorties($this->getUser());

        $filtre = new Filtre();
        $user = $participantRepository->find($this->getUser());
        $filtre->setCampus($user->getCampus());
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        $filtreForm->handleRequest($request);

        if($filtreForm->isSubmitted() && $filtreForm->isValid()){
            $filtre = $filtreForm->getData();
            $resultats = $sortieRepository->afficherSortiesFiltrees($user, $filtre);

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

    #[Route('/nouvellesortie', name: 'nouvelle')]
    public function nouvelleSortie(Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();

        /** @var Participant $user */
        $user = $this->getUser();

        $sortie->setOrganisateur($user)
            ->setSiteOrganisateur($user->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if($sortieForm->getClickedButton() && 'btnEnregistrer' === $sortieForm->getClickedButton()->getName()){
                $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
                $sortie->setEtat($etat);
            } else if($sortieForm->getClickedButton() && 'btnPublier' === $sortieForm->getClickedButton()->getName()){
                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                $sortie->setEtat($etat);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La sortie a été créée avec succès.'
            );

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sorties/nouvelle.html.twig',[
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/afficherlieux/{id}', name: 'afficher_lieux', defaults: ['id' => null])]
    public function afficherLieuxParVille(?Ville $ville): JsonResponse
    {
        if(!$ville){
            return $this->json([], RESPONSE::HTTP_BAD_REQUEST);
        }

        $listeLieux = $ville->getLieux();

        return $this->json($listeLieux, context: ['groups' => 'listeLieux']);
    }

    #[Route('/gestioninscription/{id}', name: 'inscription_sortie')]
    public function gestionInscriptionSortie(?Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if(!$sortie){
            $this->addFlash(
                'warning',
                'Aucune sortie n\'a été trouvée, veuillez contacter l\'administrateur.'
            );
            return $this->redirectToRoute('sortie_index');
        }

        /** @var Participant $participant */
        $participant = $this->getUser();
        $now = new \DateTime('now');
        if($sortie->getParticipants()->contains($participant)) {
            $sortie->removeParticipant($participant);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Vous avez bien été désinscrit de la sortie ' . $sortie->getNom() . '.'
            );
        } elseif ($sortie->getDateLimiteInscription() <= $now){
            $this->addFlash(
                'warning',
                'La date limite d\'inscription a été passée, les inscriptions sont fermées.'
            );
        } else {
            $sortie->addParticipant($participant);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre inscription à la sortie ' . $sortie->getNom() . 'a bien été prise en compte.'
            );
        }

        return $this->redirectToRoute('sortie_index');
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function afficherDetail(?Sortie $sortie): Response
    {
        if (!$sortie){
            $this->addFlash(
                'warning',
                'Aucune sortie n\'a été trouvée, veuillez contacter l\'administrateur.'
            );
            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sorties/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
