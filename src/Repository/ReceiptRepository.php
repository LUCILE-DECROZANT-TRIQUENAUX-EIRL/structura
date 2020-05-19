<?php

namespace App\Repository;

use App\Entity\Receipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Receipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Receipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Receipt[]    findAll()
 * @method Receipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Receipt::class);
    }

    /**
     * @return int[] Returns an array of all the fiscal years
     * to which one or more receipt as been associated.
     */
    public function findAvailableFiscalYears()
    {
        $results = $this->createQueryBuilder('r')
            ->select('r.fiscalYear')
            ->distinct()
            ->getQuery()
            ->getResult();

        $formatedResults = [];

        foreach($results as $result)
        {
            $formatedResults[$result['fiscalYear']] = $result['fiscalYear'];
        }

        return $formatedResults;
    }

    /**
     * @return int[] Returns an array of all the fiscal years
     * to which one or more receipt as been associated for the people
     * passed in parameters
     */
    public function findAvailableFiscalYearsByPeople($people)
    {
        $results = $this->createQueryBuilder('r')
            ->select('r.fiscalYear')
            ->join('r.payment', 'p')
            ->where('p.payer = :people')
            ->setParameter('people', $people)
            ->distinct()
            ->getQuery()
            ->getResult();

        $formatedResults = [];

        foreach ($results as $result)
        {
            $formatedResults[$result['fiscalYear']] = $result['fiscalYear'];
        }

        return $formatedResults;
    }

    /**
     * Returns the last order number for a given fiscal year.
     * @param int $fiscalYear The fiscal year for which we want the last order number.
     * @return int The last order number for the given fiscal year.
     */
    public function findLastOrderNumberForFiscalYear(int $fiscalYear)
    {
        $result = $this->createQueryBuilder('r')
            ->select('MAX(r.orderNumber) AS lastOrderNumber')
            ->where('r.fiscalYear = :fiscalYear')
            ->setParameter('fiscalYear', $fiscalYear)
            ->getQuery()
            ->getSingleResult();

        return $result['lastOrderNumber'];
    }

    /**
     * Return all the receipts between two given dates.
     * @param \DateTime $fromDate
     * @param \DateTime $toDate
     * @return Receipts[]
     */
    public function findBetweenTwoDates(\DateTime $fromDate,\DateTime $toDate)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r')
                ->join('r.payment', 'p')
                ->where('p.date_received >= :fromDate')
                ->setParameter('fromDate', $fromDate)
                ->andwhere('p.date_received <= :toDate')
                ->setParameter('toDate', $toDate);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return all the receipts corresponding to a people and a fiscal year
     * @param int $fiscalYear
     * @param type $people
     * @return Receipts[]
     */
    public function findByFiscalYearAndPeople(int $fiscalYear, $people)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r, p')
                ->join('r.payment', 'p')
                ->where('p.payer = :people')
                ->setParameter('people', $people)
                ->andwhere('r.fiscalYear = :fiscalYear')
                ->setParameter('fiscalYear', $fiscalYear);

        return $qb->getQuery()->getResult();
    }
}
