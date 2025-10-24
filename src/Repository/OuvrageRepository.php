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
}
