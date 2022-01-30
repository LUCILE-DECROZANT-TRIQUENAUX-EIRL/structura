<?php

namespace App\Repository;

use App\Entity\ReceiptsFromYearGroupingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceiptsFromYearGroupingFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptsFromYearGroupingFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptsFromYearGroupingFile[]    findAll()
 * @method ReceiptsFromYearGroupingFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptsFromYearGroupingFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptsFromYearGroupingFile::class);
    }

    /**
     * Return all ReceiptsFromYearGroupingFile that are currently being generated
     * @param int $year (default: null)
     * @return ReceiptsFromYearGroupingFile[]
     */
    public function findByGenerationInProgress($year = null)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r')
                ->join('r.receiptsGenerationBase', 'rgb')
                ->where('rgb.generationDateEnd IS null');

        if (!empty($year))
        {
            $qb->andWhere('r.year = :year')
                    ->setParameter('year', $year);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Return the last ReceiptsFromYearGroupingFile
     * @param int $year (default: null)
     * @return ReceiptsFromYearGroupingFile|null
     */
    public function findLastGenerated($year = null)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r, rgb')
                ->join('r.receiptsGenerationBase', 'rgb')
                ->where('rgb.generationDateEnd IS NOT null')
                ->orderBy('rgb.generationDateEnd', 'DESC');

        if (!empty($year))
        {
            $qb->andWhere('r.year = :year')
                    ->setParameter('year', $year);
        }

        $lastFiles = $qb->getQuery()->getResult();
        if (empty($lastFiles))
        {
            return null;
        }
        return $lastFiles[0];
    }
}
