<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher){
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        // CREATION DES CAMPUS

        $campusNantes = new Campus();
        $campusNantes->setNom('Nantes');
        $manager->persist($campusNantes);

        $campusRennes = new Campus();
        $campusRennes->setNom('Rennes');
        $manager->persist($campusRennes);

        $tableauCampus[] = $campusRennes;
        $tableauCampus[] = $campusNantes;

        // CREATION DE L'ADMIN

        $admin = new Participant();
        $admin->setNom('Haddock')
            ->setPrenom('Archibald')
            ->setPseudo('admin')
            ->setMotPasse($this->hasher->hashPassword($admin, 'Azerty123'))
            ->setEmail('admin@admin.com')
            ->setActif(true)
            ->setAdministrateur(true)
            ->setCampus($campusRennes);
        $manager->persist($admin);

        // CREATION DES PARTICIPANTS

        $tableauParticipants = [];

        for($i = 0; $i <= 10; $i++){
            $randomIndex = rand(0, count($tableauCampus) - 1);

            $participant = new Participant();
            $participant->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setPseudo('Participant'.$i)
                ->setEmail(strtolower($participant->getPrenom().'.'.$participant->getNom().'@mail.com'))
                ->setActif(true)
                ->setAdministrateur(false)
                ->setCampus($tableauCampus[$randomIndex])
                ->setMotPasse($this->hasher->hashPassword($participant, 'Azerty123'));

            $tableauParticipants[] = $participant;
            $manager->persist($participant);
        }

        // CREATION DES VILLES

        $tableauVilles = [];

        $villeNantes = new Ville();
        $villeNantes->setCodePostal('44000')->setNom('Nantes');
        $manager->persist($villeNantes);
        $tableauVilles[] = $villeNantes;

        $villeRennes = new Ville();
        $villeRennes->setCodePostal('35000')->setNom('Rennes');
        $manager->persist($villeRennes);
        $tableauVilles[] = $villeRennes;

        // CREATION DES LIEUX

        $lieuxRandom = ['Bar à jeux', 'Cathédrale', 'Musée', 'Aquarium', 'Karting', 'Cinéma', 'Parc', 'Opéra', 'Restaurant', 'Crèperie'];
        $tableauLieux = [];

        for($i = 0; $i <= count($lieuxRandom)-1; $i++){
            $randomIndex = rand(0, count($tableauVilles) - 1);

            $lieu = new Lieu();
            $lieu->setNom($lieuxRandom[$i])
                ->setVille($tableauVilles[$randomIndex])
                ->setRue($faker->streetName());
            $tableauLieux[] = $lieu;

            $manager->persist($lieu);
        }

        // CREATION DES ETATS
        $listeEtats = ['Créée', 'Ouverte', 'Clôturée', 'Activité en cours', 'Activité passée', 'Annulé'];
        $tableauEtats = [];

        for($i = 0; $i <= count($listeEtats)-1; $i++){
            $etat = new Etat();
            $etat->setLibelle($listeEtats[$i]);
            $tableauEtats[] = $etat;

            $manager->persist($etat);
        }

        // CREATION DES SORTIES

        for($i = 0; $i <= 20; $i++){
            $randomIndexOrganisateur = rand(0, count($tableauParticipants) - 1);
            $randomIndexCampus = rand(0, count($tableauCampus) - 1);
            $randomIndexLieu = rand(0, count($tableauLieux) - 1);
            $randomIndexEtat = rand(0, count($tableauEtats) - 1);

            $sortie = new Sortie();
            $sortie->setEtat($tableauEtats[$randomIndexEtat])
                ->setLieu($tableauLieux[$randomIndexLieu])
                ->setSiteOrganisateur($tableauCampus[$randomIndexCampus])
                ->setOrganisateur($tableauParticipants[$randomIndexOrganisateur])
                ->setDateHeureDebut($faker->dateTimeBetween('+1 week', '+3 week'))
                ->setDuree(rand(30, 60))
                ->setDateLimiteInscription($faker->dateTimeBetween($sortie->getDateHeureDebut()->format('Y-m-d') . '-3 day',$sortie->getDateHeureDebut()->format('Y-m-d') . '-1 day'))
                ->setInfosSortie('Lorem ipsum')
                ->setNom('Activité ' . $i)
            ->setNbInscriptionsMax(rand(5, 20));
            $manager->persist($sortie);
        }

        $manager->flush();
    }
}
