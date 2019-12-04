<?php

namespace App\Repository;

use App\Entity\PaymentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentType[]    findAll()
 * @method PaymentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentType::class);
    }

    public function findByAmountTooBigForMembership() {
        $entityManager = $this->getEntityManager();

        // All people with active membership
        $query = $entityManager->createQuery(
           'SELECT p FROM App\Entity\Payment p
            JOIN p.membership m
            JOIN m.type t
            WHERE p.amount > t.defaultAmount'
        );

        return $query->execute();
    }
}
