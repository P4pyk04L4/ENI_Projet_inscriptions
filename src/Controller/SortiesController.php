<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\AnnulationSortieType;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\SortiesChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/sorties', name: 'sortie_')]
class SortiesController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(SortieRepository $sortieRepository,
                          ParticipantRepository $participantRepository,
                          Request $request, SortiesChecker $sortiesChecker): Response
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

//        foreach ($sorties as $sortie){
//            $sortie = $sortiesChecker->checkSorties($sortie);
//        }

        return $this->render('sorties/index.html.twig', [
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

            if($request->request->has('btnEnregistrer')){
                $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
                $sortie->setEtat($etat);
            } else if($request->request->has('btnPublier')){
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
            'fonction' => 'creation',
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    #[Route('/afficherlieux/{id}', name: 'afficher_lieux', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function afficherLieuxParVille(Ville $ville): JsonResponse
    {
        $listeLieux = $ville->getLieux();

        return $this->json($listeLieux, context: ['groups' => 'listeLieux']);
    }

    #[Route('/gestioninscription/{id}', name: 'inscription_sortie', requirements: ['id' => '\d+'])]
    public function gestionInscriptionSortie(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        /** @var Participant $participant */
        $participant = $this->getUser();
        $now = new \DateTime('now');
        if($sortie->getParticipants()->contains($participant)) {
            if($sortie->getDateHeureDebut() < $now){
                $this->addFlash(
                    'warning',
                    'La sortie ' . $sortie->getNom() . ' a déjà débuté, vous ne pouvez plus vous désinscrire.'
                );
            } else {
                $sortie->removeParticipant($participant);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Vous avez bien été désinscrit de la sortie ' . $sortie->getNom() . '.'
                );
            }

        } elseif ($sortie->getDateLimiteInscription() <= $now){
            $this->addFlash(
                'warning',
                'La date limite d\'inscription a été passée, les inscriptions sont fermées.'
            );
        } else {
            if($sortie->getEtat()->getLibelle() === "Ouverte"){
                $sortie->addParticipant($participant);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre inscription à la sortie ' . $sortie->getNom() . ' a bien été prise en compte.'
                );
            } else {
                $this->addFlash(
                    'warning',
                    'L\'inscription à la sortie ' . $sortie->getNom() . 'est impossible. Veuillez contacter l\'administrateur ou l\'irganisateur de la sortie.'
                );
            }
        }

        return $this->redirectToRoute('sortie_index');
    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function afficherDetail(Sortie $sortie): Response
    {
        return $this->render('sorties/detail.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/annuler/{id}', name: 'annuler', requirements: ['id' => '\d+'])]
    public function annulerSortie(Sortie $sortie, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository){

        if($sortie->getOrganisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException('Accès refusé');
        }

        $now = new \DateTime('now');
        if($sortie->getDateHeureDebut() < $now){
            $this->addFlash(
                'danger',
                'Il est impossible d\'annuler une sortie qui a déjà débuté.'
            );

            $this->redirectToRoute('sortie_index');
        }

        $annulationForm = $this->createForm(AnnulationSortieType::class);
        $annulationForm->handleRequest($request);


        if($annulationForm->isSubmitted() && $annulationForm->isValid()){
            $nouvelleDescription = $sortie->getInfosSortie() . ' /// SORTIE ANNULEE /// Motif : ' . $annulationForm->get('motifAnnulation')->getData();
            $sortie->setInfosSortie($nouvelleDescription);
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La sortie a bien été annulée.'
            );

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sorties/annulation.html.twig', [
            'sortie' => $sortie,
            'annulationForm' => $annulationForm->createView()
        ]);
    }

    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifierSortie(Sortie $sortie, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($sortie->getOrganisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La sortie a été modifiée avec succès.'
            );

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sorties/nouvelle.html.twig',
        [
            'fonction' => 'modification',
            'sortieForm' => $sortieForm,
            'sortie' => $sortie

        ]);
    }
}
