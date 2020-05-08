<?php

namespace App\Repository;

use App\Entity\ReceiptsGenerationFromTwoDates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReceiptsGenerationFromTwoDates|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsGenerationFromTwoDates|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsGenerationFromTwoDates[]    findAll()
 * @method ReceiptsGenerationFromTwoDates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsGenerationFromTwoDatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsGenerationFromTwoDates::class);
    }

    // /**
    //  * @return ReceiptsGenerationFromTwoDates[] Returns an array of ReceiptsGenerationFromTwoDates objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReceiptsGenerationFromTwoDates
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
