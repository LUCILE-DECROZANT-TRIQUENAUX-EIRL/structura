<?php

namespace App\Repository;

use App\Entity\ReceiptsGroupingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceiptsGroupingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsGroupingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsGroupingFile[]    findAll()
 * @method ReceiptsGroupingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsGenerationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsGroupingFile::class);
    }

    // /**
    //  * @return ReceiptsGeneration[] Returns an array of ReceiptsGeneration objects
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
    public function findOneBySomeField($value): ?ReceiptsGeneration
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
