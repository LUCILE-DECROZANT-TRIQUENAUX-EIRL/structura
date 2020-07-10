<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findBetweenTwoDates(\DateTimeInterface $from, \DateTimeInterface $to)
    {
        $fromDay = new \DateTime($from->format("Y-m-d")." 00:00:00");
        $toDay   = new \DateTime($to->format("Y-m-d")." 23:59:59");

        $qb = $this->createQueryBuilder("p");
        $qb
            ->where('p.date_cashed BETWEEN :from AND :to')
            ->setParameter('from', $fromDay)
            ->setParameter('to', $toDay)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Custom findAll method not getting all the fields
     * to avoid incompatibilities in case the Payment class would be modified
     * in the migration 20200511151231
     *
     * @return Payment[]
     */
    public function findAllForMigration20200511151231()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array Raw data about revenues
     */
    public function getRevenuesRecap()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $currentDate = new \DateTime();
        $fromDate = strtotime('-11 months', $currentDate->getTimestamp());
        $fromDateFormated = date('Y-m-', $fromDate) . '01 00:00:00';

        $sql = 'SELECT SUM(payment.amount) as revenues, '
                . 'DATE_FORMAT(payment.date_cashed, "%Y-%m") as date '
                . 'FROM payment '
                . 'WHERE payment.date_cashed > ? '
                . 'GROUP BY DATE_FORMAT(payment.date_cashed, "%Y-%m")';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $fromDateFormated);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }
}
