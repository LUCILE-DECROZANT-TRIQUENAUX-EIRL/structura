<?php
/**
 * Repository for the People
 */

namespace App\Repository;


use App\Entity\People;
use App\Entity\PeopleType;

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
            )
            ORDER BY p.lastName'
        );

        $query->setParameter(':now', new \DateTime());

        return $query->execute();
    }

    public function findWithActiveMembership()
    {
        $entityManager = $this->getEntityManager();

        // All people with active membership
        $query = $entityManager->createQuery(
           'SELECT p2, m FROM App\Entity\People p2
            JOIN p2.memberships m
            WHERE m.date_start <= :now
            AND m.date_end > :now
            ORDER BY p2.lastName'
        );

        $query->setParameter(':now', new \DateTime());

        return $query->execute();
    }

    /**
     * Return people who at least once had subscribed to the association
     * but do not have an active one now.
     *
     * @return array People[] the people who had once a membership but not anymore
     */
    public function findWithOutdatedMembership(): array
    {
        $entityManager = $this->getEntityManager();

        // All people with no active memberships
        $query = $entityManager->createQuery(
           'SELECT p
            FROM App\Entity\People p
            WHERE p.id NOT IN (
                SELECT p2.id FROM App\Entity\People p2
                JOIN p2.memberships m1
                WHERE m1.date_start <= :now
                AND m1.date_end > :now
            )
            AND p.id IN (
                SELECT p3.id FROM App\Entity\People p3
                JOIN p3.memberships m2
            )
            ORDER BY p.lastName'
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
            )
            ORDER BY p.lastName'
        );

        return $query->execute();
    }

    public function findContacts(): array
    {
        $entityManager = $this->getEntityManager();

        // All people with a Contact type
        $query = $entityManager->createQuery(
           'SELECT p
            FROM App\Entity\People p
            JOIN p.types t
            WHERE t.code = :code
            ORDER BY p.lastName'
        );

        $query->setParameter(':code', PeopleType::CONTACT_CODE);

        return $query->execute();
    }

    public function findPeopleWithAddress(): array
    {
        $entityManager = $this->getEntityManager();

        // All people with a Contact type
        $query = $entityManager->createQuery(
           'SELECT p, a
            FROM App\Entity\People p
            LEFT JOIN p.addresses a
            WHERE a.line IS NOT NULL
            AND a.postalCode IS NOT NULL
            AND a.city IS NOT NULL
            ORDER BY p.lastName ASC'
        );

        return $query->execute();
    }
}
