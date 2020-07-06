<?php

namespace App\Repository;

use App\Entity\PeopleType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PeopleType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeopleType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeopleType[]    findAll()
 * @method PeopleType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeopleTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeopleType::class);
    }
}
