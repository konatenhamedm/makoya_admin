<?php

namespace App\Repository;

use App\Entity\WorkflowServicePrestataire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkflowServicePrestataire>
 *
 * @method WorkflowServicePrestataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkflowServicePrestataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkflowServicePrestataire[]    findAll()
 * @method WorkflowServicePrestataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkflowServicePrestataireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkflowServicePrestataire::class);
    }

    public function save(WorkflowServicePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WorkflowServicePrestataire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return WorkflowServicePrestataire[] Returns an array of WorkflowServicePrestataire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorkflowServicePrestataire
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
