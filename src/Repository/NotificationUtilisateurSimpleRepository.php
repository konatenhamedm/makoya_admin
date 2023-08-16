<?php

namespace App\Repository;

use App\Entity\NotificationUtilisateurSimple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotificationUtilisateurSimple>
 *
 * @method NotificationUtilisateurSimple|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationUtilisateurSimple|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationUtilisateurSimple[]    findAll()
 * @method NotificationUtilisateurSimple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationUtilisateurSimpleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationUtilisateurSimple::class);
    }

    public function save(NotificationUtilisateurSimple $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NotificationUtilisateurSimple $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NotificationUtilisateurSimple[] Returns an array of NotificationUtilisateurSimple objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NotificationUtilisateurSimple
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
