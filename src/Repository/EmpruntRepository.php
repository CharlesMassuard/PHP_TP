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

    public function pourcentageExemplairesEmpruntes(): float
    {
        $em = $this->getEntityManager();

        $totalExemplaires = $em->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('App\Entity\Exemplaires', 'e')
            ->getQuery()
            ->getSingleScalarResult();

        $exemplairesEmpruntes = $em->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('App\Entity\Emprunt', 'e')
            ->where('e.statut = :statut')
            ->setParameter('statut', 'Emprunté')
            ->getQuery()
            ->getSingleScalarResult();

        if ($totalExemplaires == 0) {
            return 0.0;
        }

        return round(($exemplairesEmpruntes / $totalExemplaires) * 100, 2);
    }

    public function delaiEmpruntsMoyen(): float
    {
        $emprunts = $this->createQueryBuilder('e')
            ->select('e.dateRetour, e.dateEmprunt')
            ->where('e.statut = :statut')
            ->setParameter('statut', 'Retourné')
            ->getQuery()
            ->getResult();

        if (empty($emprunts)) {
            return 0.0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($emprunts as $emprunt) {
            if ($emprunt['dateRetour'] && $emprunt['dateEmprunt']) {
                $diff = $emprunt['dateRetour']->diff($emprunt['dateEmprunt']);
                $totalDays += $diff->days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 2) : 0.0;
    }
}
