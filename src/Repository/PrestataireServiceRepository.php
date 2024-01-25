<?php

namespace App\Repository;

use App\Entity\PrestataireService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrestataireService>
 *
 * @method PrestataireService|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrestataireService|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrestataireService[]    findAll()
 * @method PrestataireService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestataireServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrestataireService::class);
    }

    public function save(PrestataireService $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(PrestataireService $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getServices($id)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
            ->innerJoin('c.sousCategorie', 's')
            ->innerJoin('c.image', 'i')
            ->innerJoin('c.service', 'ser')
            ->innerJoin('c.prestataire', 'p')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getServicesAll()
    {
        return $this->createQueryBuilder('c')
            ->addSelect('c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
            ->innerJoin('c.sousCategorie', 's')
            ->innerJoin('c.image', 'i')
            ->innerJoin('c.service', 'ser')
            ->innerJoin('c.prestataire', 'p')
            ->orderBy('c.countVisite', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    public function getServicesAllC()
    {
        return $this->createQueryBuilder('c')
            ->addSelect('cm.message,c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
            ->innerJoin('c.sousCategorie', 's')
            ->innerJoin('c.image', 'i')
            ->innerJoin('c.commentaires', 'cm')
            ->innerJoin('c.service', 'ser')
            ->innerJoin('c.prestataire', 'p')
            /* ->orderBy('c.countVisite', 'ASC') */
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PrestataireService[] Returns an array of PrestataireService objects
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

    //    public function findOneBySomeField($value): ?PrestataireService
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
