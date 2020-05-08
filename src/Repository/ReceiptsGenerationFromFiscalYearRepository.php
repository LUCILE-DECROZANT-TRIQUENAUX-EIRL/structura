<?php

namespace App\Repository;

use App\Entity\ReceiptsGenerationFromFiscalYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReceiptsGenerationFromFiscalYear|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsGenerationFromFiscalYear|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsGenerationFromFiscalYear[]    findAll()
 * @method ReceiptsGenerationFromFiscalYear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsGenerationFromFiscalYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsGenerationFromFiscalYear::class);
    }

    // /**
    //  * @return ReceiptsGenerationFromFiscalYear[] Returns an array of ReceiptsGenerationFromFiscalYear objects
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
    public function findOneBySomeField($value): ?ReceiptsGenerationFromFiscalYear
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
