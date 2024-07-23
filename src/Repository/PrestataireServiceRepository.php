<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Civilite;
use App\Entity\Commune;
use App\Entity\Employe;
use App\Entity\Prestataire;
use App\Entity\PrestataireService;
use App\Entity\Quartier;
use App\Entity\ServicePrestataire;
use App\Entity\SousCategorie;
use App\Entity\UserFront;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurSimple;
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
    use TableInfoTrait;
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



    public function getNombre($id, $type)
    {

        if ($type == 'localite') {
            $data = $this->createQueryBuilder('c')
                ->select('count(distinct co.id)')
                ->innerJoin('c.categorie', 'ca')
                ->innerJoin('c.prestataire', 'p')
                ->innerJoin('p.quartier', 'q')
                ->innerJoin('q.commune', 'co')
                ->andWhere('ca.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleScalarResult();
        } elseif ($type == 'prestataire') {
            $data = $this->createQueryBuilder('c')
                ->select('count(distinct p.id)')
                ->innerJoin('c.categorie', 'ca')
                ->innerJoin('c.prestataire', 'p')
                ->andWhere('ca.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $data = $this->createQueryBuilder('c')
                ->select('count(c.id)')
                ->innerJoin('c.categorie', 'ca')
                ->innerJoin('c.prestataire', 'p')
                ->andWhere('ca.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $data;
    }


    public function getAllLocalite($id)
    {
        return $this->createQueryBuilder('c')
            ->select('distinct co.id ,co.code,co.nom,sp.nom as sou_p')
            ->innerJoin('c.categorie', 'ca')
            ->innerJoin('c.prestataire', 'p')
            ->innerJoin('p.quartier', 'q')
            ->innerJoin('q.commune', 'co')
            ->innerJoin('co.sousPrefecture', 'sp')
            ->andWhere('ca.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
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
    public function getServicesBySearch($idCategorie, $search, $ville)
    {
        $sql = $this->createQueryBuilder('c')
            ->addSelect('c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
            ->innerJoin('c.categorie', 's')
            ->innerJoin('c.image', 'i')
            ->innerJoin('c.service', 'ser')
            ->innerJoin('c.prestataire', 'p')
            ->innerJoin('p.quartier', 'q')
            ->innerJoin('q.commune', 'co');
        /*    ->andWhere('s.id = :id')
            ->andWhere('ser.libelle LIKE :search'); */
        ///dd($search);
        if ($search != "null") {
            $sql->andWhere('ser.libelle LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        if ($idCategorie != "null") {
            $sql->andWhere('s.id = :id')
                ->setParameter('id', $idCategorie);
        }
        if ($ville != "null") {
            $sql->andWhere('co.id = :ville')
                ->setParameter('ville', $ville);
        }

        return  $sql->orderBy('c.id', 'ASC')
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
    public function getServicesAllC(bool $size = false)
    {
        if ($size) {
            $data =  $this->createQueryBuilder('c')
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
        } else {
            $data = $this->createQueryBuilder('c')
                ->addSelect('cm.message,c.id,c.countVisite,i.path,i.alt,s.id sId,s.libelle sousCategorie,ser.id serId,ser.libelle service,p.id pId,p.denominationSociale,p.contactPrincipal,p.statut')
                ->innerJoin('c.sousCategorie', 's')
                ->innerJoin('c.image', 'i')
                ->innerJoin('c.commentaires', 'cm')
                ->innerJoin('c.service', 'ser')
                ->innerJoin('c.prestataire', 'p')
                ->getQuery()
                ->getResult();
        }

        return $data;
    }




    public function getCategorieByNombrePrestataire($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
FROM {$tablePrestataireService} d
JOIN {$tableCategrorie} c ON c.id = d.categorie_id
WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            WHERE   DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateFin 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateDebut  
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
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

    public function getEffectifByLocaliteAndCategorie($dateDebut, $dateFin, $localite)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null && $localite != null) {
            $sql = <<<SQL
SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
FROM {$tablePrestataireService} d
JOIN {$tableCategrorie} c ON c.id = d.categorie_id
JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
JOIN {$tableUser} u ON u.id = p.id
JOIN {$tableQuartier} q ON q.id = u.quartier_id
JOIN {$tableCommune} co ON co.id = q.commune_id
WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin AND co.id = :localite
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null && $localite != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableCommune} co ON co.id = q.commune_id
            WHERE   DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateFin AND co.id = :localite
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null && $localite != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableCommune} co ON co.id = q.commune_id
            WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateDebut AND co.id = :localite 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut == null && $dateFin == null && $localite != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableCommune} co ON co.id = q.commune_id
            WHERE   co.id = :localite 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tableCategrorie} c ON c.id = d.categorie_id
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableCommune} co ON co.id = q.commune_id
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;
        $params['localite'] = $localite;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }

    public function getNombreFournisseur($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        $sql = <<<SQL
        SELECT COUNT( DISTINCT p.id) AS _total
        FROM {$tablePrestataire} p
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        ORDER BY _total DESC
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getAllFournisseur($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableUtilisateur = $this->getTableName(Utilisateur::class, $em);
        $tableEmploye = $this->getTableName(Employe::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        $sql = <<<SQL
        SELECT p.denomination_sociale as nomP,u.email as email,emp.nom as nom,emp.prenom as prenom
        FROM {$tablePrestataire} p
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableUtilisateur} e ON e.id = u.user_add_id
        JOIN {$tableEmploye} emp ON emp.id = e.employe_id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        GROUP BY nomP,email,nom,prenom,contact
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getNombreUtilisateur($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUtilisateur = $this->getTableName(UtilisateurSimple::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        $sql = <<<SQL
        SELECT COUNT( DISTINCT p.id) AS _total
        FROM {$tableUtilisateur} p
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        ORDER BY _total DESC
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getAllUtilisateur($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUtilisateur = $this->getTableName(UtilisateurSimple::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);
        $tableCivilite = $this->getTableName(Civilite::class, $em);

        $sql = <<<SQL
        SELECT p.nom as nom,p.prenoms as prenom,u.email as email,p.contact as contact,g.code as genre
        FROM {$tableUtilisateur} p
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableCivilite} g ON g.id = p.genre_id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        GROUP BY nom,prenom,email,contact,genre
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getNombreCategorie($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUtilisateur = $this->getTableName(UtilisateurSimple::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);

        $sql = <<<SQL
        SELECT COUNT( DISTINCT c.id) AS _total
        FROM {$tablePrestataireService} d
        JOIN {$tableCategrorie} c ON c.id = d.categorie_id
        JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getAllCategorie($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUtilisateur = $this->getTableName(UtilisateurSimple::class, $em);
        $tableCommune = $this->getTableName(Commune::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);

        $sql = <<<SQL
        SELECT COUNT( DISTINCT c.id) AS _total, c.libelle as libelle,c.code as code
        FROM {$tablePrestataireService} d
        JOIN {$tableCategrorie} c ON c.id = d.categorie_id
        JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
        JOIN {$tableUser} u ON u.id = p.id
        JOIN {$tableQuartier} q ON q.id = u.quartier_id
        JOIN {$tableCommune} co ON co.id = q.commune_id
        WHERE   co.id = :id 
        GROUP BY libelle,code
        SQL;

        $params['id'] = $id;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }



    public function getSousCategorieByNombrePrestataire($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableSousCategrorie = $this->getTableName(SousCategorie::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
FROM {$tablePrestataireService} d
LEFT JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            LEFT JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            WHERE   DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateFin 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            LEFT JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateDebut  
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, c.libelle as categorie
            FROM {$tablePrestataireService} d
            LEFT JOIN {$tableSousCategrorie} c ON c.id = d.sous_categorie_id
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getServiceByNombrePrestataire($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tableService = $this->getTableName(ServicePrestataire::class, $em);

        //dd($dateDebut,$dateFin);

        if ($dateDebut != null && $dateFin != null) {
            $sql = <<<SQL
SELECT COUNT( DISTINCT d.prestataire_id) AS _total, s.libelle as service
FROM {$tablePrestataireService} d
JOIN {$tableService} s ON s.id = d.service_id
WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY service
ORDER BY _total DESC
SQL;
        } elseif ($dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, s.libelle as service
            FROM {$tablePrestataireService} d 
            JOIN {$tableService} s ON s.id = d.service_id
            WHERE   DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateFin 
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        } elseif ($dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, s.libelle as service
            FROM {$tablePrestataireService} d
            JOIN {$tableService} s ON s.id = d.service_id
            WHERE  DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateDebut  
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        } else {
            $sql = <<<SQL
            SELECT COUNT( DISTINCT d.prestataire_id) AS _total, s.libelle as service
            FROM {$tablePrestataireService} d
            JOIN {$tableService} s ON s.id = d.service_id
            GROUP BY service
            ORDER BY _total DESC
            SQL;
        }


        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }


    public function getTauxCategorie($localite, $dateDebut, $dateFin)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableLocalite = $this->getTableName(Commune::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        //dd($dateDebut,$dateFin);

        if ($localite != null && $dateDebut != null && $dateFin) {
            $sql = <<<SQL
SELECT COUNT(DISTINCT p.id) AS _total, s.libelle as categorie
FROM {$tablePrestataireService} d
JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
JOIN {$tableUser} u ON u.id = p.id
JOIN {$tableQuartier} q ON q.id = u.quartier_id
JOIN {$tableLocalite} l ON l.id = q.commune_id
JOIN {$tableCategrorie} s ON s.id = d.categorie_id
WHERE  l.id = :localite AND DATE_FORMAT(d.date_creation,"%d/%m/%Y") BETWEEN :dateDebut AND :dateFin
GROUP BY categorie
ORDER BY _total DESC
SQL;
        } elseif ($localite != null && $dateDebut != null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT(DISTINCT p.id) AS _total, s.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = d.categorie_id
            WHERE  l.id = :localite AND DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateDebut 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($localite != null && $dateDebut == null && $dateFin != null) {
            $sql = <<<SQL
            SELECT COUNT(DISTINCT p.id) AS _total, s.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = d.categorie_id
            WHERE  l.id = :localite AND DATE_FORMAT(d.date_creation,"%d/%m/%Y") = :dateFin
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($localite == null && $dateDebut == null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT( p.id) AS _total, s.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = d.categorie_id
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        } elseif ($localite != null && $dateDebut == null && $dateFin == null) {
            $sql = <<<SQL
            SELECT COUNT( p.id) AS _total, s.libelle as categorie
            FROM {$tablePrestataireService} d
            JOIN {$tablePrestataire} p ON p.id = d.prestataire_id
            JOIN {$tableUser} u ON u.id = p.id
            JOIN {$tableQuartier} q ON q.id = u.quartier_id
            JOIN {$tableLocalite} l ON l.id = q.commune_id
            JOIN {$tableCategrorie} s ON s.id = d.categorie_id
            WHERE  l.id = :localite 
            GROUP BY categorie
            ORDER BY _total DESC
            SQL;
        }


        $params['localite'] = $localite;
        $params['dateDebut'] = $dateDebut;
        $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
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
