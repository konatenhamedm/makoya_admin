<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Fichier;
use App\Entity\NombreClick;
use App\Entity\PrestataireService;
use App\Entity\ServicePrestataire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServicePrestataire>
 *
 * @method ServicePrestataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServicePrestataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServicePrestataire[]    findAll()
 * @method ServicePrestataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicePrestataireRepository extends ServiceEntityRepository
{
    use TableInfoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServicePrestataire::class);
    }

    public function save(ServicePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ServicePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getServiceCategorie($id)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('s.id,s.libelle')
            ->innerJoin('s.categorie', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }


    public function getServicePlusVisite($categorie)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableService = $this->getTableName(ServicePrestataire::class, $em);
        $tabImage = $this->getTableName(Fichier::class, $em);
        $tabCategorie = $this->getTableName(Categorie::class, $em);

        //dd($dateDebut,$dateFin);

        $sql = <<<SQL
        SELECT SUM(d.nombre) AS _total, s.libelle as service,s.id as service_id,CONCAT(i.path,'/',i.alt) as image
        FROM {$tablePrestataireService} c
        LEFT JOIN {$tableNombreClick} d ON c.id = d.service_id
        JOIN {$tabImage} i ON i.id = c.image_id
        JOIN {$tableService} s ON s.id = c.service_id
        JOIN {$tabCategorie} cat ON cat.id = c.categorie_id
        WHERE cat.code = :categorie
        GROUP BY service,service_id,image
        ORDER BY _total DESC
        LIMIT 4
        SQL;
        $params['categorie'] = $categorie;





        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }


    //    /**
    //     * @return ServicePrestataire[] Returns an array of ServicePrestataire objects
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

    //    public function findOneBySomeField($value): ?ServicePrestataire
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
