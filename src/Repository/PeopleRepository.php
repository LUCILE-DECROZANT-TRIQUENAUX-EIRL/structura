<?php
/**
 * Repository for the People
 */

namespace App\Repository;


use App\Entity\People;

/**
 * PeopleRepository
 *
 */
class PeopleRepository extends \Doctrine\ORM\EntityRepository
{
    public function findWithNoActiveMembership(): array
    {
        $entityManager = $this->getEntityManager();

        // All people with no active memberships
        $query = $entityManager->createQuery(
           'SELECT p
            FROM App\Entity\People p
            WHERE p.id NOT IN (
                SELECT p2.id FROM App\Entity\People p2
                JOIN p2.memberships m
                WHERE m.date_start <= :now
                AND m.date_end > :now
            )'
        );

        $query->setParameter(':now', new \DateTime());

        return $query->execute();
    }

    public function findWithActiveMembership()
    {
        $entityManager = $this->getEntityManager();

        // All people with active membership
        $query = $entityManager->createQuery(
           'SELECT p2.id FROM App\Entity\People p2
            JOIN p2.memberships m
            WHERE m.date_start <= :now
            AND m.date_end > :now'
        );

        $query->setParameter(':now', new \DateTime());

        return $query->execute();
    }

    public function findWithNoMembership()
    {
        $entityManager = $this->getEntityManager();

        // All people with no membership
        $query = $entityManager->createQuery(
           'SELECT p
            FROM App\Entity\People p
            WHERE p.id NOT IN (
                SELECT p2.id FROM App\Entity\People p2
                JOIN p2.memberships m
            )'
        );

        return $query->execute();
    }
}