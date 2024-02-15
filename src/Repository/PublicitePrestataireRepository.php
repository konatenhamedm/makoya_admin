<?php

namespace App\Repository;

use App\Entity\PublicitePrestataire;
use App\Entity\UserFront;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicitePrestataire>
 *
 * @method PublicitePrestataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicitePrestataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicitePrestataire[]    findAll()
 * @method PublicitePrestataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicitePrestataireRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicitePrestataire::class);
    }

    public function save(PublicitePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PublicitePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /*     public function getListeRecouvrementParEtudiant()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $tableInfo = $this->getTableName(PublicitePrestataire::class, $em);
        $tablePreinscription = $this->getTableName(UserFront::class, $em);


        //dd($dateDebut,$dateFin);


        $sql = <<<SQL
SELECT e.id AS _etudiant_id,p.nom,p.prenom,f.montant_preinscription,d.etat
FROM {$tablePreinscription} d
Left JOIN {$tableInfo} i ON i.preinscription_id = d.id
Inner JOIN {$tableUser} e ON e.id = d.etudiant_id
Inner JOIN {$tableNiveau} n ON n.id = d.niveau_id
Inner JOIN {$tablePersonne} p ON p.id = e.id
Inner JOIN {$tableFiliere} f ON f.id = n.filiere_id
WHERE  d.niveau_id = :niveau and d.etat  in ('attente_paiement','valide','paiement_confirmation')

SQL;


        $params[''] = '';
        // $params['dateFin'] = $dateFin;


        $stmt = $connection->executeQuery($sql);
        return $stmt->fetchAllAssociative();
    } */

    //    /**
    //     * @return PublicitePrestataire[] Returns an array of PublicitePrestataire objects
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

    //    public function findOneBySomeField($value): ?PublicitePrestataire
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
