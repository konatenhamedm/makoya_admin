<?php

namespace App\Repository;

use App\Entity\NombreClick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NombreClick>
 *
 * @method NombreClick|null find($id, $lockMode = null, $lockVersion = null)
 * @method NombreClick|null findOneBy(array $criteria, array $orderBy = null)
 * @method NombreClick[]    findAll()
 * @method NombreClick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NombreClickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NombreClick::class);
    }

    public function save(NombreClick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NombreClick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getData($mac, $type, $id, $quartier): ?NombreClick
    {

        if ($type == 'categorie') {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.categorie', 'categorie')
                ->andWhere('categorie.id = :id')
                ->andWhere('a.quartier = :quartier')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->setParameter('quartier', $quartier)
                ->getQuery()
                ->getOneOrNullResult();
        } elseif ($type == 'service') {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.service', 'service')
                ->andWhere('service.id = :id')
                ->andWhere('a.quartier = :quartier')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->setParameter('quartier', $quartier)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.sousCategorie', 'sousCategorie')
                ->andWhere('sousCategorie.id = :id')
                ->andWhere('a.quartier = :quartier')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->setParameter('quartier', $quartier)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $data;
    }
    public function getDataWithoutQuartier($mac, $type, $id): ?NombreClick
    {

        if ($type == 'categorie') {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.categorie', 'categorie')
                ->andWhere('categorie.id = :id')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } elseif ($type == 'service') {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.service', 'service')
                ->andWhere('service.id = :id')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        } else {
            $data = $this->createQueryBuilder('a')
                ->andWhere('a.mac = :mac')
                ->innerJoin('a.sousCategorie', 'sousCategorie')
                ->andWhere('sousCategorie.id = :id')
                ->setParameter('mac', $mac)
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $data;
    }

    public function getLastNumero($annee)
    {
        $annee = substr($annee, -2);
        //KPL-O-{AN}XXX
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        return $this->createQueryBuilder('a')
            ->select("a.numero")

            ->orderBy('CAST(SUBSTRING(a.numero, -3) AS UNSIGNED)', 'DESC')
            ->andWhere('SUBSTRING(a.numero, 7, 2) = :annee')
            ->setParameter('annee', $annee)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function generateNumero($annee)
    {
        $data = $this->getLastNumero($annee);

        if ($data) {
            $numero = substr($data['numero'], -3);
            $numero = ltrim($numero, '0');
        } else {
            $numero = 0;
        }
        return 'KPL-P-' . substr($annee, -2) . str_pad(($numero + 1), 3, '0', STR_PAD_LEFT);
    }

    //    /**
    //     * @return NombreClick[] Returns an array of NombreClick objects
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

    //    public function findOneBySomeField($value): ?NombreClick
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
