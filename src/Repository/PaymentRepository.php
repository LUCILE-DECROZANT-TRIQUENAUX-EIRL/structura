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

}
