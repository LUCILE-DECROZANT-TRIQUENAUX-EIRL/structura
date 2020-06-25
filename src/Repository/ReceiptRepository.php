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
     * @return int[] Returns an array of all the years
     * to which one or more receipt as been associated.
     */
    public function findAvailableYears()
    {
        $results = $this->createQueryBuilder('r')
            ->select('r.year')
            ->distinct()
            ->getQuery()
            ->getResult();

        $formatedResults = [];

        foreach($results as $result)
        {
            $formatedResults[$result['year']] = $result['year'];
        }

        return $formatedResults;
    }

    /**
     * @return int[] Returns an array of all the years
     * to which one or more receipt as been associated for the people
     * passed in parameters
     */
    public function findAvailableYearsByPeople($people)
    {
        $results = $this->createQueryBuilder('r')
            ->select('r.year')
            ->join('r.payment', 'p')
            ->where('p.payer = :people')
            ->setParameter('people', $people)
            ->distinct()
            ->getQuery()
            ->getResult();

        $formatedResults = [];

        foreach ($results as $result)
        {
            $formatedResults[$result['year']] = $result['year'];
        }

        return $formatedResults;
    }

    /**
     * Returns the last order number for a given year.
     * @param int $year The year for which we want the last order number.
     * @return int The last order number for the given year.
     */
    public function findLastOrderNumberForAYear(int $year)
    {
        $result = $this->createQueryBuilder('r')
            ->select('MAX(r.orderNumber) AS lastOrderNumber')
            ->where('r.year = :year')
            ->setParameter('year', $year)
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
     * Return all the receipts corresponding to a people and a year
     * @param int $year
     * @param type $people
     * @return Receipts[]
     */
    public function findByYearAndPeople(int $year, $people)
    {
        $qb = $this->createQueryBuilder('r')
                ->select('r, p')
                ->join('r.payment', 'p')
                ->where('p.payer = :people')
                ->setParameter('people', $people)
                ->andwhere('r.year = :year')
                ->setParameter('year', $year);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return all receipts, used in migration, should not be edited nor deleted
     *
     * @return array
     */
    public function findByAllForVersion20200515180231()
    {
        $connexion = $this->getEntityManager()
            ->getConnection();
        $sql = 'SELECT * FROM receipt';
        $query = $connexion->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Update receipt order code, used in migration, should not be edited nor deleted
     *
     * @param int $id id of receipt needing update
     * @param string $orderCode new order code
     */
    public function updateOrderCodeForVersion20200515180231($id, $orderCode)
    {
        $connexion = $this->getEntityManager()
            ->getConnection();
        $sql = 'UPDATE receipt SET order_code=:order_code WHERE id=:id';
        $query = $connexion->prepare($sql);
        $query->execute([
            'id' => $id,
            'order_code' => $orderCode,
        ]);
    }
}
