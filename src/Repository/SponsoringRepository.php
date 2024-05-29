<?php

namespace App\Repository;

use App\Entity\Sponsoring;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sponsoring>
 *
 * @method Sponsoring|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sponsoring|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sponsoring[]    findAll()
 * @method Sponsoring[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsoringRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsoring::class);
    }

    /**
     * @return Sponsoring[] Returns an array of Sponsoring objects
     */
    public function getSponsoring(): array
    {
        $date = new DateTime();
        $result = $date->format('Y-m-d');


        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', 'demande_valider')
            ->andWhere('DATE_DIFF(s.dateDebut , :datedebut) < 0 or DATE_DIFF(s.dateDebut , :datedebut) =0')
            ->andWhere('DATE_DIFF(s.dateFin , :datedebut) >= 0 ')
            ->setParameter('datedebut', new \DateTime($result))
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Sponsoring
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
