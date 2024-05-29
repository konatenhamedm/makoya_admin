<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Commune;
use App\Entity\NombreClick;
use App\Entity\Prestataire;
use App\Entity\PrestataireService;
use App\Entity\Quartier;
use App\Entity\ServicePrestataire;
use App\Entity\SousCategorie;
use App\Entity\UserFront;
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
    use TableInfoTrait;
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


    public function getNombreVue($id)
    {


        return $this->createQueryBuilder('a')
            ->select('sum(a.nombre)')
            ->innerJoin('a.categorie', 'categorie')
            ->andWhere('categorie.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
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



    public function getCategorieByNombreVue($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT SUM(d.nombre) AS _total, c.libelle as categorie
FROM {$tableNombreClick} d
JOIN {$tableCategrorie} c ON c.id = d.categorie_id
WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            WHERE   DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateFin 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateDebut  
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getSousCategorieByNombreVue($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tableSousCategrorie = $this->getTableName(SousCategorie::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT SUM(d.nombre) AS _total, c.libelle as categorie
FROM {$tableNombreClick} d
JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            WHERE   DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateFin 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateDebut  
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, c.libelle as categorie
            FROM {$tableNombreClick} d
            JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getServiceByNombreVue($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableService = $this->getTableName(ServicePrestataire::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT SUM(d.nombre) AS _total, s.libelle as service
FROM {$tableNombreClick} d
JOIN {$tablePrestataireService} c ON c.id = d.service_id
JOIN {$tableService} s ON s.id = c.service_id
WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY service
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, s.libelle as service
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            JOIN {$tableService} s ON s.id = c.service_id
            WHERE   DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateFin 
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, s.libelle as service
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            LEFT JOIN {$tableService} s ON s.id = c.service_id
            WHERE  DATE_FORMAT(d.date_modification,"%d/%m/%Y") = :dateDebut  
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, s.libelle as service
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            LEFT JOIN {$tableService} s ON s.id = c.service_id
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getClassementEntreprise($localite, $categorie)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableLocalite = $this->getTableName(Commune::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        //dd($dateDebut,$dateFin);

        if ($localite != null && $categorie != null) {
            $sql = <<<SQL
SELECT SUM(d.nombre) AS _total, p.denomination_sociale as fournisseur
FROM {$tableNombreClick} d
JOIN {$tablePrestataireService} c ON c.id = d.service_id
JOIN {$tablePrestataire} p ON p.id = c.prestataire_id
JOIN {$tableUser} u ON u.id = p.id
JOIN {$tableQuartier} q ON q.id = u.quartier_id
JOIN {$tableLocalite} l ON l.id = q.commune_id
JOIN {$tableCategrorie} s ON s.id = c.categorie_id
WHERE  l.id = :localite AND s.id = :categorie
GROUP BY fournisseur
ORDER BY _total DESC
SQL;
        } elseif ($localite == null && $categorie != null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, p.denomination_sociale as fournisseur
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            JOIN {$tablePrestataire} p ON p.id = c.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = c.categorie_id
            WHERE s.id = :categorie
            GROUP BY fournisseur
            ORDER BY _total DESC
            SQL;
        } elseif ($localite != null && $categorie == null) {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, p.denomination_sociale as fournisseur
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            JOIN {$tablePrestataire} p ON p.id = c.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = c.categorie_id
            WHERE  l.id = :localite 
            GROUP BY fournisseur
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, p.denomination_sociale as fournisseur
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            JOIN {$tablePrestataire} p ON p.id = c.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = c.categorie_id
            GROUP BY fournisseur
            ORDER BY _total DESC
            SQL;
        }


        $params['localite'] = $localite;
        $params['categorie'] = $categorie;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getImprimeClassementEntreprise()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNombreClick = $this->getTableName(NombreClick::class, $em);
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableLocalite = $this->getTableName(Commune::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        //dd($dateDebut,$dateFin);



        //dd("");
        $sql = <<<SQL
            SELECT SUM(d.nombre) AS _total, p.denomination_sociale as fournisseur,p.contact_principal as contact,u.email as email,u.date_creation as dateCreation
            FROM {$tableNombreClick} d
            JOIN {$tablePrestataireService} c ON c.id = d.service_id
            JOIN {$tablePrestataire} p ON p.id = c.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = c.categorie_id
            GROUP BY fournisseur,contact,email,dateCreation
            ORDER BY _total DESC
            SQL;



        /*  $params['localite'] = $localite;
        $params['categorie'] = $categorie; */


        $stmt = $connection->executeQuery($sql);

        return $stmt->fetchAllAssociative();
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
