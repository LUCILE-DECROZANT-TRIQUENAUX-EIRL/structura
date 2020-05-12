<?php

namespace App\Repository;

use App\Entity\ReceiptsFromFiscalYearGroupingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReceiptsFromFiscalYearGroupingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsFromFiscalYearGroupingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsFromFiscalYearGroupingFile[]    findAll()
 * @method ReceiptsFromFiscalYearGroupingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsFromFiscalYearGroupingFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsFromFiscalYearGroupingFile::class);
    }

}
