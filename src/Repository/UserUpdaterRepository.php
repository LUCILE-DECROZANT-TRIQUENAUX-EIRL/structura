<?php

namespace App\Repository;

use App\Entity\UserUpdater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserUpdater|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserUpdater|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserUpdater[]    findAll()
 * @method UserUpdater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserUpdaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserUpdater::class);
    }
}
