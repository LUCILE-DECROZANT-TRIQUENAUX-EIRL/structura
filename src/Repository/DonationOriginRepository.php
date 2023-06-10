<?php

namespace App\Repository;

use App\Entity\DonationOrigin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DonationOrigin>
 *
 * @method DonationOrigin|null find($id, $lockMode = null, $lockVersion = null)
 * @method DonationOrigin|null findOneBy(array $criteria, array $orderBy = null)
 * @method DonationOrigin[]    findAll()
 * @method DonationOrigin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationOriginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DonationOrigin::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DonationOrigin $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(DonationOrigin $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return DonationOrigin[] Returns an array of DonationOrigin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DonationOrigin
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
