<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Donation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donation[]    findAll()
 * @method Donation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }

    /**
     * Find all donations ordered by their donator last name then the donation date.
     *
     * @return array The donation list ordered by theit donator last name then the donation date.
     */
    public function findOrderedDonations()
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.donator','p')
            ->orderBy('p.lastName')
            ->orderBy('d.donation_date', 'DESC')
            ->getQuery()
            ->getResult();
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

        $sql = 'SELECT SUM(donation.amount) as revenues, '
                . 'DATE_FORMAT(payment.date_cashed, "%Y-%m") as date '
                . 'FROM payment '
                . 'JOIN donation on donation.payment_id = payment.id '
                . 'WHERE payment.date_cashed > ? '
                . 'GROUP BY DATE_FORMAT(payment.date_cashed, "%Y-%m")';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $fromDateFormated);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }

}
