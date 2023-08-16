<?php

namespace App\Repository;

use App\Entity\PubliciteRegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PubliciteRegion>
 *
 * @method PubliciteRegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PubliciteRegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PubliciteRegion[]    findAll()
 * @method PubliciteRegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteRegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PubliciteRegion::class);
    }

    public function save(PubliciteRegion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PubliciteRegion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PubliciteRegion[] Returns an array of PubliciteRegion objects
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

//    public function findOneBySomeField($value): ?PubliciteRegion
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
