<?php

namespace App\Repository;

use App\Entity\ReceiptsFromTwoDatesGroupingFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * Return all ReceiptsFromTwoDatesGroupingFile that are currently being generated
     * @param \DateTime $from (default: null)
     * @param \DateTime $to (default: null)
     * @return ReceiptsFromTwoDatesGroupingFile[]
     */
    public function findByGenerationInProgress(
        \DateTimeInterface $from = null,
        \DateTimeInterface $to = null
    )
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r')
                ->join('r.receiptsGenerationBase', 'rgb')
                ->where('rgb.generationDateEnd IS null');

        if (!empty($from) && !empty($to))
        {
            $qb->andWhere('r.dateFrom = :dateFrom')
                    ->andWhere('r.dateTo = :dateTo')
                    ->setParameter('dateFrom', $from)
                    ->setParameter('dateTo', $to);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Return the last ReceiptsFromTwoDatesGroupingFile
     *
     * @param \DateTime $from (default: null)
     * @param \DateTime $to (default: null)
     * @return ReceiptsFromTwoDatesGroupingFile|null
     */
    public function findLastGenerated(
        \DateTimeInterface $from = null,
        \DateTimeInterface $to = null
    )
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r, rgb')
                ->join('r.receiptsGenerationBase', 'rgb')
                ->where('rgb.generationDateEnd IS NOT null')
                ->orderBy('rgb.generationDateEnd', 'DESC');

        if (!empty($from) && !empty($to))
        {
            $qb->andWhere('r.dateFrom = :dateFrom')
                    ->andWhere('r.dateTo = :dateTo')
                    ->setParameter('dateFrom', $from)
                    ->setParameter('dateTo', $to);
        }

        $lastFiles = $qb->getQuery()->getResult();
        if (empty($lastFiles))
        {
            return null;
        }

        return $lastFiles[0];
    }
}
