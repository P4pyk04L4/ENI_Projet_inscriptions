<?php

namespace App\Repository;

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

    public function afficherSortiesFiltrees(?Participant $user, ?Campus $campus, ?string $nomSortie, ?\DateTime $dateDebut, ?\DateTime $dateFin,
                                            ?bool $estOrganisateur, ?bool $estInscrit, ?bool $nEstPasInscrit, ?bool $sortiesPassees)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftJoin('s.organisateur', 'org')->addSelect('org');
        $queryBuilder->leftJoin('s.participants', 'part')->addSelect('part');
        $queryBuilder->leftJoin('s.lieu', 'li')->addSelect('li');
        $queryBuilder->leftJoin('s.siteOrganisateur', 'campus')->addSelect('campus');
        $queryBuilder->leftJoin('s.etat', 'e')->addSelect('e');

        if($campus){
            $queryBuilder->andWhere('s.siteOrganisateur = :campus')
                ->setParameter('campus', $campus);
        }
        if($nomSortie){
            $queryBuilder->andWhere('s.nom LIKE :nomSortie' )
                ->setParameter('nomSortie', '%' . $nomSortie . '%');
        }
        if($dateDebut){
            $queryBuilder->andWhere('s.dateHeureDebut > :dateDebut')->setParameter('dateDebut', $dateDebut);
        }
        if($dateFin){
            $queryBuilder->andWhere('s.dateHeureDebut < :dateFin')->setParameter('dateFin', $dateFin);
        }
        if($estOrganisateur){
            $queryBuilder->andWhere('s.organisateur = :organisateur')->setParameter('organisateur', $user);
        }
        if($estInscrit && !$nEstPasInscrit){
            $queryBuilder->andWhere(':participant MEMBER OF s.participants')->setParameter('participant', $user);
        }
        if($nEstPasInscrit && !$estInscrit){
            $queryBuilder->andWhere(':participant NOT MEMBER OF s.participants')->setParameter('participant', $user);
        }
        if($sortiesPassees){
            $now = new \DateTime("now");
            $queryBuilder->andWhere('s.dateHeureDebut < :now')->setParameter('now', $now);
        }

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
