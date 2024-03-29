<?php

namespace App\Repository;

use App\Entity\Civilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Civilite>
 *
 * @method Civilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Civilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Civilite[]    findAll()
 * @method Civilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CiviliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Civilite::class);
    }

    public function add(Civilite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCivilites()
    {
        return $this->createQueryBuilder('c')
            ->addSelect('c.id,c.libelle')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function remove(Civilite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllByListId($value): array
    {
        return $this->createQueryBuilder('c')
            ->andwhere('c.id IN (:ids)')
            ->setParameter('ids', $value)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Civilite[] Returns an array of Civilite objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Civilite
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
