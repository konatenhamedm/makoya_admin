<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Fichier;
use App\Entity\Publicite;
use App\Entity\PubliciteCategorie;
use App\Entity\PubliciteDemande;
use App\Entity\PubliciteEncart;
use App\Entity\PubliciteImage;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PubliciteImage>
 *
 * @method PubliciteImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PubliciteImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PubliciteImage[]    findAll()
 * @method PubliciteImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PubliciteImageRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PubliciteImage::class);
    }

    public function save(PubliciteImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PubliciteImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPubliciteEncart_old($ordre): array
    {
        $date = new DateTime();
        $result = $date->format('Y-m-d');


        return $this->createQueryBuilder('s')
            ->innerJoin('s.publicite', 'p')
            ->andWhere('DATE_DIFF(p.dateDebut , :datedebut) < 0 or DATE_DIFF(p.dateDebut , :datedebut) =0')
            ->andWhere('DATE_DIFF(p.dateFin , :datedebut) >= 0 ')
            /* ->andWhere('p.ordre = :ordre')
            ->setParameter('ordre', $ordre) */
            ->setParameter('datedebut', new \DateTime($result))
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function getPubliciteEncart($ordre)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $publicteImage = $this->getTableName(PubliciteImage::class, $em);
        $publicite = $this->getTableName(Publicite::class, $em);
        $publiciteEncart = $this->getTableName(PubliciteEncart::class, $em);
        $image = $this->getTableName(Fichier::class, $em);

        $date = new DateTime();
        $result = $date->format('Y-m-d');

        $sql = <<<SQL
            SELECT d.id,i.alt,i.path,c.libelle,d.lien,d.description
            FROM {$publicteImage} d
            JOIN {$image} i ON i.id = d.image_id
            JOIN {$publicite} c ON c.id = d.publicite_id
            JOIN {$publiciteEncart} u ON u.id = c.id
            WHERE u.ordre = :ordre AND (DATEDIFF(c.date_debut , :datedebut) < 0 or DATEDIFF(c.date_debut , :datedebut) =0 ) AND DATEDIFF(c.date_fin , :datedebut) >= 0
            SQL;

        $params['datedebut'] = $result;
        $params['ordre'] = $ordre;

        /*  $params['localite'] = $localite;
        $params['categorie'] = $categorie; */


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }

    public function getPublicitecategorie($id)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $publicteImage = $this->getTableName(PubliciteImage::class, $em);
        $publicite = $this->getTableName(Publicite::class, $em);
        $publiciteCategorie = $this->getTableName(PubliciteCategorie::class, $em);
        $categorie = $this->getTableName(Categorie::class, $em);
        $image = $this->getTableName(Fichier::class, $em);

        $date = new DateTime();
        $result = $date->format('Y-m-d');

        $sql = <<<SQL
            SELECT d.id,i.alt,i.path,c.libelle,d.lien,d.description
            FROM {$publicteImage} d
            JOIN {$image} i ON i.id = d.image_id
            JOIN {$publicite} c ON c.id = d.publicite_id
            JOIN {$publiciteCategorie} u ON u.id = c.id
            WHERE u.categorie_id = :id AND (DATEDIFF(c.date_debut , :datedebut) < 0 or DATEDIFF(c.date_debut , :datedebut) =0 ) AND DATEDIFF(c.date_fin , :datedebut) >= 0
            SQL;

        $params['datedebut'] = $result;
        $params['id'] = $id;

        /*  $params['localite'] = $localite;
        $params['categorie'] = $categorie; */


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }
    public function getPubliciteUsers()
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $publicteImage = $this->getTableName(PubliciteImage::class, $em);
        $publicite = $this->getTableName(Publicite::class, $em);
        $publiciteCategorie = $this->getTableName(PubliciteCategorie::class, $em);
        $categorie = $this->getTableName(Categorie::class, $em);
        $image = $this->getTableName(Fichier::class, $em);

        $date = new DateTime();
        //$result = $date->format('Y-m-d');
        $id = 17;

        $sql = <<<SQL
            SELECT d.id,i.alt,i.path,c.libelle,d.lien,d.description
            FROM {$publicteImage} d
            JOIN {$image} i ON i.id = d.image_id
            JOIN {$publicite} c ON c.id = d.publicite_id
            JOIN {$publiciteCategorie} u ON u.id = c.id
            WHERE  (DATEDIFF(c.date_debut , :datedebut) < 0 or DATEDIFF(c.date_debut , :datedebut) =0 ) AND DATEDIFF(c.date_fin , :datedebut) >= 0
            SQL;


        $params['datedebut'] = $date->format('Y-m-d');
        $params['id'] = $id;
        // $params['ordre'] = $ordre;

        /*  $params['localite'] = $localite;
        $params['categorie'] = $categorie; */


        $stmt = $connection->executeQuery($sql, $params);

        return $stmt->fetchAllAssociative();
    }


    //    /**
    //     * @return PubliciteImage[] Returns an array of PubliciteImage objects
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

    //    public function findOneBySomeField($value): ?PubliciteImage
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
