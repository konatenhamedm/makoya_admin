<?php

namespace App\Repository;

use App\Entity\PubliciteEncart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PubliciteEncart>
 *
 * @method PubliciteEncart|null find($id, $lockMode = null, $lockVersion = null)
 * @method PubliciteEncart|null findOneBy(array $criteria, array $orderBy = null)
 * @method PubliciteEncart[]    findAll()
 * @method PubliciteEncart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteEncartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PubliciteEncart::class);
    }

    public function save(PubliciteEncart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PubliciteEncart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PubliciteEncart[] Returns an array of PubliciteEncart objects
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

//    public function findOneBySomeField($value): ?PubliciteEncart
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
