<?php

namespace App\Repository;

use App\Entity\ReceiptsFromTwoDatesGroupingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReceiptsFromTwoDatesGroupingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsFromTwoDatesGroupingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsFromTwoDatesGroupingFile[]    findAll()
 * @method ReceiptsFromTwoDatesGroupingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsGenerationFromTwoDatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsFromTwoDatesGroupingFile::class);
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
