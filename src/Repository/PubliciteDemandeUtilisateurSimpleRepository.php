<?php

namespace App\Repository;

use App\Entity\PubliciteDemandeUtilisateurSimple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PubliciteDemandeUtilisateurSimple>
 *
 * @method PubliciteDemandeUtilisateurSimple|null find($id, $lockMode = null, $lockVersion = null)
 * @method PubliciteDemandeUtilisateurSimple|null findOneBy(array $criteria, array $orderBy = null)
 * @method PubliciteDemandeUtilisateurSimple[]    findAll()
 * @method PubliciteDemandeUtilisateurSimple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteDemandeUtilisateurSimpleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PubliciteDemandeUtilisateurSimple::class);
    }

    public function save(PubliciteDemandeUtilisateurSimple $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PubliciteDemandeUtilisateurSimple $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PubliciteDemandeUtilisateurSimple[] Returns an array of PubliciteDemandeUtilisateurSimple objects
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

//    public function findOneBySomeField($value): ?PubliciteDemandeUtilisateurSimple
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
