<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Commune;
use App\Entity\Note;
use App\Entity\Prestataire;
use App\Entity\PrestataireService;
use App\Entity\Quartier;
use App\Entity\UserFront;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function save(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function noteSerice($service)
    {
        return $this->createQueryBuilder('n')
            ->select('count(n.id) nombre')
            ->innerJoin('n.service', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $service)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getClassementEntreprise($localite, $categorie)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNote = $this->getTableName(Note::class, $em);
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
FROM {$tableNote} d
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
            FROM {$tableNote} d
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
            FROM {$tableNote} d
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
            FROM {$tableNote} d
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
    public function getAvisEntreprises($localite, $categorie)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableNote = $this->getTableName(Note::class, $em);
        $tablePrestataireService = $this->getTableName(PrestataireService::class, $em);
        $tablePrestataire = $this->getTableName(Prestataire::class, $em);
        $tableQuartier = $this->getTableName(Quartier::class, $em);
        $tableLocalite = $this->getTableName(Commune::class, $em);
        $tableCategrorie = $this->getTableName(Categorie::class, $em);
        $tableUser = $this->getTableName(UserFront::class, $em);

        //dd($dateDebut,$dateFin);

        if ($localite != null && $categorie != null) {
            $sql = <<<SQL
SELECT SUM(d.note)/COUNT(d.id) AS _total, p.denomination_sociale as fournisseur
FROM {$tableNote} d
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
            SELECT SUM(d.note)/COUNT(d.id) AS _total, p.denomination_sociale as fournisseur
            FROM {$tableNote} d
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
            SELECT SUM(d.note)/COUNT(d.id) AS _total, p.denomination_sociale as fournisseur
            FROM {$tableNote} d
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
            SELECT SUM(d.note)/COUNT(d.id)  AS _total, p.denomination_sociale as fournisseur,COUNT(d.id) n
            FROM {$tableNote} d
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
    //    /**
    //     * @return Note[] Returns an array of Note objects
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

    //    public function findOneBySomeField($value): ?Note
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
