<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function save(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getServicesAll()
    {
        return $this->createQueryBuilder('cm')
            //->addSelect('cm.message,c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
            ->innerJoin('cm.service', 'ser')
            ->innerJoin('ser.prestataireServices', 'c')
            /* ->innerJoin('c.sousCategorie', 's')
            ->innerJoin('c.image', 'i')
            ->innerJoin('c.prestataire', 'p') */
            /*  ->orderBy('c.countVisite', 'ASC') */
            /* ->setMaxResults(5) */
            ->getQuery()
            ->getResult();
    }

    public function noteService($service)
    {
        return $this->createQueryBuilder('n')
            ->select('count(n.id) nombre')
            ->innerJoin('n.service', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $service)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Commentaire[] Returns an array of Commentaire objects
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

    //    public function findOneBySomeField($value): ?Commentaire
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
