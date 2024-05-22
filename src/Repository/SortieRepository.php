<?php

namespace App\Repository;

use App\Classe\Filtre;
use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllSorties()
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.organisateur', 'org')->addSelect('org');
        $queryBuilder->leftJoin('s.participants', 'part')->addSelect('part');
        $queryBuilder->leftJoin('s.lieu', 'li')->addSelect('li');
        $queryBuilder->leftJoin('s.siteOrganisateur', 'campus')->addSelect('campus');
        $queryBuilder->leftJoin('s.etat', 'e')->addSelect('e');

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function findAllActiveSorties(?Participant $user)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.organisateur', 'org')->addSelect('org')
        ->leftJoin('org.photosProfil', 'photosProfil')->addSelect('photosProfil');
        $queryBuilder->leftJoin('s.participants', 'part')->addSelect('part');
        $queryBuilder->leftJoin('s.lieu', 'li')->addSelect('li');
        $queryBuilder->leftJoin('s.siteOrganisateur', 'campus')->addSelect('campus');
        $queryBuilder->leftJoin('s.etat', 'e')->addSelect('e');

        # Il est possible d'ajouter des éléments de tris dans les tableaux
        $queryBuilder->andWhere('(e.libelle NOT IN (:etatInterdit) OR (e.libelle IN (:etatPerso) AND s.organisateur = :user))')
            ->setParameter('etatInterdit', [''])
            ->setParameter('etatPerso', ['Créée'])
            ->setParameter('user', $user);

        $now = new \DateTime("now");
        $dateLimite = $now->modify('-1 month');
        $queryBuilder
            ->andWhere('s.dateHeureDebut > :dateLimite')
            ->setParameter('dateLimite', $dateLimite);

        # MISE DES ACTIVITES PASSEES EN BAS DU TABLEAU, PUIS PAR DATE
        $queryBuilder->addSelect("(CASE WHEN e.libelle like 'Activité passée' THEN 0 ELSE 1 END) AS HIDDEN ORD");
        $queryBuilder->addOrderBy('ORD', 'DESC');

        $queryBuilder->addOrderBy('s.dateHeureDebut', 'ASC');

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function afficherSortiesFiltrees(?Participant $user, Filtre $filtre)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.organisateur', 'org')->addSelect('org');
        $queryBuilder->leftJoin('s.participants', 'part')->addSelect('part');
        $queryBuilder->leftJoin('s.lieu', 'li')->addSelect('li');
        $queryBuilder->leftJoin('s.siteOrganisateur', 'campus')->addSelect('campus');
        $queryBuilder->leftJoin('s.etat', 'e')->addSelect('e');

        if($filtre->getCampus()){
            $queryBuilder->andWhere('s.siteOrganisateur = :campus')
                ->setParameter('campus', $filtre->getCampus());
        }
        if($filtre->getNomSortie()){
            $queryBuilder->andWhere('s.nom LIKE :nomSortie' )
                ->setParameter('nomSortie', '%' . $filtre->getNomSortie() . '%');
        }
        if($filtre->getDateDebut()){
            $queryBuilder->andWhere('s.dateHeureDebut > :dateDebut')->setParameter('dateDebut', $filtre->getDateDebut());
        }
        if($filtre->getDateFin()){
            $queryBuilder->andWhere('s.dateHeureDebut < :dateFin')->setParameter('dateFin', $filtre->getDateFin());
        }
        if($filtre->getEstOrganisateur()){
            $queryBuilder->andWhere('s.organisateur = :organisateur')->setParameter('organisateur', $user);
        }
        if($filtre->getEstInscrit() && !$filtre->getNEstPasInscrit()){
            $queryBuilder->andWhere(':participant MEMBER OF s.participants')->setParameter('participant', $user);
        }
        if($filtre->getNEstPasInscrit() && !$filtre->getEstInscrit()){
            $queryBuilder->andWhere(':participant NOT MEMBER OF s.participants')->setParameter('participant', $user);
        }
        if($filtre->getSortiesPassees()){
            $now = new \DateTime("now");
            $queryBuilder->andWhere('e.libelle IN (:etatPasse)')->setParameter('etatPasse', ['Activité passée']);
        }

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
