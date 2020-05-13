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

    /**
     * Return all ReceiptsFromFiscalYearGroupingFile that are currently being generated
     * @param int $fiscalYear (default: null)
     * @return ReceiptsFromFiscalYearGroupingFile[]
     */
    public function findByGenerationInProgress($fiscalYear = null)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r')
                ->join('r.receiptsGenerationBase', 'rgb')
                ->where('rgb.generationDateEnd IS null');

        if (!empty($fiscalYear))
        {
            $qb->andWhere('r.fiscalYear = :fiscalYear')
                    ->setParameter('fiscalYear', $fiscalYear);
        }

        return $qb->getQuery()->getResult();
    }
}
