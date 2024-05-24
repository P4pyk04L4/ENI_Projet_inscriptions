<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\FiltreCampus;
use App\Entity\FiltreVille;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\FiltreCampusType;
use App\Form\FiltreVilleType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[Route('/campus', name: 'campus')]
    public function afficherCampus(CampusRepository $campusRepository,
                                   Request $request,
                                   EntityManagerInterface $entityManager): Response
    {
        $campus = $campusRepository->findAll();

        $newCampus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $newCampus);
        $campusForm->handleRequest($request);

        $filtreCampus = new FiltreCampus();
        $filtreCampusForm = $this->createForm(FiltreCampusType::class, $filtreCampus);
        $filtreCampusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()){
            $entityManager->persist($newCampus);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le nouveau campus a bien été créé.'
            );

            return $this->redirectToRoute('admin_campus');
        }

        if($filtreCampusForm->isSubmitted() && $filtreCampusForm->isValid()){
            if($filtreCampusForm->getClickedButton() && 'searchButton' === $filtreCampusForm->getClickedButton()->getName()) {
                $campus = $campusRepository->afficherCampusFiltresParNom($filtreCampus->getNomCampus());
            } elseif ($filtreCampusForm->getClickedButton() && 'resetButton' === $filtreCampusForm->getClickedButton()->getName()){
                $campus = $campusRepository->findAll();
            }

        }

        return $this->render('admin/campus.html.twig', [
            'campus' => $campus,
            'campusForm' =>$campusForm,
            'filtreCampus' => $filtreCampusForm
        ]);
    }

    #[Route('/villes', name: 'villes')]
    public function afficherVilles(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $villes = $villeRepository->findAll();

        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        $filtreVille = new FiltreVille();
        $filtreVilleForm = $this->createForm(FiltreVilleType::class, $filtreVille);
        $filtreVilleForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La ville a bien été ajoutée à la base de données.'
            );

            return $this->redirectToRoute('admin_villes');
        }

        if($filtreVilleForm->isSubmitted() && $filtreVilleForm->isValid()){

            if($filtreVilleForm->getClickedButton() && 'searchButton' === $filtreVilleForm->getClickedButton()->getName()){
                $villes = $villeRepository->afficherCillesFiltreesParNom($filtreVille->getNomVille());
            } elseif ($filtreVilleForm->getClickedButton() && 'resetButton' === $filtreVilleForm->getClickedButton()->getName()){
                $villes= $villeRepository->findAll();
            }

        }

        return $this->render('admin/villes.html.twig', [
            'villes' => $villes,
            'villeForm' => $villeForm,
            'filtreVilleForm' => $filtreVilleForm
        ]);
    }
}
