<?php

namespace App\Repository;

use App\Entity\Responsibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Responsibility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Responsibility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Responsibility[]    findAll()
 * @method Responsibility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsibilityRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Responsibility::class);
    }
}
