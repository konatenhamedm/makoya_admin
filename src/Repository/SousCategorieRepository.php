<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Fichier;
use App\Entity\NombreClick;
use App\Entity\SousCategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousCategorie>
 *
 * @method SousCategorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousCategorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousCategorie[]    findAll()
 * @method SousCategorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousCategorieRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousCategorie::class);
    }

    public function save(SousCategorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SousCategorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getSousCategorie($id)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('s.id,s.libelle')
            ->innerJoin('s.categorie', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }


    public function getSousCategorieByVisite($code)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tabSousCategorie = $this->getTableName(SousCategorie::class, $em);
        //$tablePrestataireService = $this->getTableName(Categorie::class, $em);
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tabImage = $this->getTableName(Fichier::class, $em);
        $tabCategorie = $this->getTableName(Categorie::class, $em);

        //dd($dateDebut,$dateFin);

        $sql = <<<SQL
        /* SELECT SUM(d.nombre) AS _total, s.libelle as service,s.id as service_id,CONCAT(i.path,'/',i.alt) as image */
        SELECT SUM(d.nombre) AS _total, s.libelle as libelle,s.id as id,CONCAT(i.path,'/',i.alt) as image 
        FROM {$tabSousCategorie} s
        JOIN {$tabCategorie} cat ON cat.id = s.categorie_id
        LEFT JOIN  {$tabImage} i ON i.id = s.image_id
        LEFT JOIN {$tableNombreClick} d ON s.id = d.sous_categorie_id
       
        WHERE cat.code = :code
        GROUP BY libelle,id,image
        ORDER BY _total DESC
        LIMIT 4
        SQL;
        $params['code'] = $code;





        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }

    //    /**
    //     * @return SousCategorie[] Returns an array of SousCategorie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SousCategorie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
