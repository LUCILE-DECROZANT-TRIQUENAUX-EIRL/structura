<?php

namespace App\Repository;

use App\Entity\MembershipType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipType[]    findAll()
 * @method MembershipType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipType::class);
    }

    // /**
    //  * @return MembershipType[] Returns an array of MembershipType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MembershipType
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
