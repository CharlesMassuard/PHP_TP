<?php

namespace App\Repository;

use App\Entity\Ouvrage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ouvrage>
 */
class OuvrageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ouvrage::class);
    }

    public function isDisponible(int $id): bool
    {
        $ouvrage = $this->find($id);
        if (!$ouvrage) {
            throw new \InvalidArgumentException('Ouvrage non trouvé');
        }

        foreach ($ouvrage->getExemplaires() as $exemplaire) {
            if ($exemplaire->getDisponibilite()) {
                return true;
            }
        }

        return false;
    }

    public function getExemplaires(int $id): array
    {
        $ouvrage = $this->find($id);
        if (!$ouvrage) {
            throw new \InvalidArgumentException('Ouvrage non trouvé');
        }

        return $ouvrage->getExemplaires()->toArray();
    }

    public function findDistinctCategories(): array
    {
        // récupère toutes les catégories (colonne JSON) et aplatit
        $rows = $this->createQueryBuilder('o')
            ->select('o.categories')
            ->getQuery()
            ->getArrayResult();

        $set = [];
        foreach ($rows as $r) {
            $val = $r['categories'] ?? $r['categories'] ?? null;
            if (!$val) continue;
            $arr = is_array($val) ? $val : json_decode($val, true);
            if (!is_array($arr)) continue;
            foreach ($arr as $c) $set[trim($c)] = trim($c);
        }
        ksort($set);
        return $set;
    }

    public function findDistinctLangues(): array
    {
        $rows = $this->createQueryBuilder('o')
            ->select('o.langues')
            ->getQuery()
            ->getArrayResult();

        $set = [];
        foreach ($rows as $r) {
            $val = $r['langues'] ?? null;
            if (!$val) continue;
            $arr = is_array($val) ? $val : json_decode($val, true);
            if (!is_array($arr)) continue;
            foreach ($arr as $l) $set[trim($l)] = trim($l);
        }
        ksort($set);
        return $set;
    }

    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('o');

        if (!empty($criteria['titre'])) {
            $qb->andWhere('LOWER(o.Titre) LIKE :titre')
               ->setParameter('titre', '%' . mb_strtolower($criteria['titre']) . '%');
        }

        if (!empty($criteria['categories'])) {
            $or = $qb->expr()->orX();
            foreach ($criteria['categories'] as $i => $cat) {
                $p = 'cat' . $i;
                $or->add($qb->expr()->like('LOWER(o.categories)', ':' . $p));
                $qb->setParameter($p, '%' . mb_strtolower($cat) . '%');
            }
            $qb->andWhere($or);
        }

        if (!empty($criteria['langues'])) {
            $or = $qb->expr()->orX();
            foreach ($criteria['langues'] as $i => $lang) {
                $p = 'lang' . $i;
                $or->add($qb->expr()->like('LOWER(o.langues)', ':' . $p));
                $qb->setParameter($p, '%' . mb_strtolower($lang) . '%');
            }
            $qb->andWhere($or);
        }

        if (!empty($criteria['year_from'])) {
            $from = new \DateTimeImmutable((int)$criteria['year_from'] . '-01-01');
            $qb->andWhere('o.annee >= :from')->setParameter('from', $from);
        }
        if (!empty($criteria['year_to'])) {
            $to = new \DateTimeImmutable((int)$criteria['year_to'] . '-12-31');
            $qb->andWhere('o.annee <= :to')->setParameter('to', $to);
        }

        if (!empty($criteria['disponible'])) {
            if ($criteria['disponible'] === 'yes') {
                $qb->innerJoin('o.exemplaires', 'e')->andWhere('e.disponibilite = true');
            } elseif ($criteria['disponible'] === 'no') {
                // exemples simplifiés — adapte selon ton mapping
                $qb->leftJoin('o.exemplaires', 'e')
                   ->andWhere($qb->expr()->orX($qb->expr()->isNull('e.id'), $qb->expr()->eq('e.disponibilite', ':false')))
                   ->setParameter('false', false);
            }
        }

        $qb->orderBy('o.titre', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
