<?php

namespace App\Repository;

use App\Entity\PubliciteDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PubliciteDemande>
 *
 * @method PubliciteDemande|null find($id, $lockMode = null, $lockVersion = null)
 * @method PubliciteDemande|null findOneBy(array $criteria, array $orderBy = null)
 * @method PubliciteDemande[]    findAll()
 * @method PubliciteDemande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PubliciteDemande::class);
    }

    public function save(PubliciteDemande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PubliciteDemande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PubliciteDemande[] Returns an array of PubliciteDemande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PubliciteDemande
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
