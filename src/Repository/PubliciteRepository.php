<?php

namespace App\Repository;

use App\Entity\Publicite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicite>
 *
 * @method Publicite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicite[]    findAll()
 * @method Publicite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicite::class);
    }

    public function save(Publicite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publicite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPubliciteBy()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.code LIKE :cat or e.code LIKE :rgp or e.code LIKE :enc')
            ->setParameter('cat', '%CAT%')
            ->setParameter('rgp', '%RGP%')
            ->setParameter('enc', '%ENC%')
            ->getQuery()
            ->getResult();
    }

    public function getPubTypeLibelle($type, $id)
    {

        $data =    $this->createQueryBuilder('p')
            ->andWhere('p.code = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();


        if ($type == "CAT") {
            return $data->getCategorie()->getLibelle();
        } elseif ($type == "ENC") {
            return "Encart";
        } elseif ($type == "RGP") {
            return $data->getRegion()->getNom();
        }
    }

    //    /**
    //     * @return Publicite[] Returns an array of Publicite objects
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

    //    public function findOneBySomeField($value): ?Publicite
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
