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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sorties', name: 'sortie_')]
class SortiesController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(SortieRepository $sortieRepository,ParticipantRepository $participantRepository, Request $request): Response
    {
        $sorties = $sortieRepository->findAllSorties();

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
    public function afficherLieuxParVille(?Ville $ville)
    {
        if(!$ville){
            return $this->json([], RESPONSE::HTTP_BAD_REQUEST);
        }

        $listeLieux = $ville->getLieux();

        return $this->json($listeLieux, context: ['groups' => 'listeLieux']);
    }
}
