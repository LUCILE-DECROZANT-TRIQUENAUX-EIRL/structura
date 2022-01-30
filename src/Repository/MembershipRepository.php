<?php

namespace App\Repository;

use App\Entity\Membership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Membership|null find($id, $lockMode = null, $lockVersion = null)
 * @method Membership|null findOneBy(array $criteria, array $orderBy = null)
 * @method Membership[]    findAll()
 * @method Membership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Membership::class);
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

        $sql = 'SELECT SUM(membership.amount) as revenues, '
            . 'DATE_FORMAT(payment.date_cashed, "%Y-%m") as date '
            . 'FROM payment '
            . 'JOIN membership on membership.payment_id = payment.id '
            . 'WHERE payment.date_cashed > :date_cashed '
            . 'GROUP BY DATE_FORMAT(payment.date_cashed, "%Y-%m")';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['date_cashed' => $fromDateFormated]);

        return $result->fetchAllAssociative();
    }
}
