<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function findEmpruntsExpirantDate(\DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.dateRetour = :date')
            ->setParameter('date', $date->format('Y-m-d'));

        return $qb->getQuery()->getResult();
    }

    public function findEmpruntsEnRetard(): array
    {
        $currentDate = new \DateTimeImmutable('now');
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.dateRetour < :currentDate')
            ->andWhere('e.statut = :statut')
            ->setParameter('currentDate', $currentDate->format('Y-m-d'))
            ->setParameter('statut', 'Emprunté');

        return $qb->getQuery()->getResult();
    }
}
