<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[Route('/campus', name: 'campus')]
    public function afficherCampus(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = $campusRepository->findAll();

        $newCampus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $newCampus);
        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()){
            $entityManager->persist($newCampus);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le nouveau campus a bien été créé.'
            );

            return $this->redirectToRoute('admin_campus');
        }

        return $this->render('admin/campus.html.twig', [
            'campus' => $campus,
            'campusForm' =>$campusForm
        ]);
    }
}
