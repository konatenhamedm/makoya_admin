<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Region>
 *
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function save(Region $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Region $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNbrePrestataireUserSimpleByRegions($val)
    {
        return $this->createQueryBuilder('r')
            ->select(' u.reference,u.email,u.username')
            ->innerJoin('r.departements', 'd')
            ->innerJoin('d.sousPrefectures', 's')
            ->innerJoin('s.communes', 'c')
            ->innerJoin('c.quartiers', 'q')
            ->innerJoin('q.userFronts', 'u')
            ->andWhere('r.code = :val')
            ->setParameter('val', $val)
            /* ->groupBy('u.reference') */
            ->getQuery()
            ->getResult();
    }

    public function getInfos($val)
    {
        return $this->createQueryBuilder('r')
            ->select(' u.reference,u.email,u.username,r.code')
            ->innerJoin('r.departements', 'd')
            ->innerJoin('d.sousPrefectures', 's')
            ->innerJoin('s.communes', 'c')
            ->innerJoin('c.quartiers', 'q')
            ->innerJoin('q.userFronts', 'u')
            ->andWhere('r.code = :val')
            ->setParameter('val', $val)
            /* ->groupBy('u.reference') */
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Region[] Returns an array of Region objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Region
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
