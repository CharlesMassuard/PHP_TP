<?php

namespace App\Service;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AuditLogger
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function log(string $actionEffectuee, array $details = []): void
    {
        $user = $this->security->getUser();

        $log = new AuditLog(
            $actionEffectuee,
            $details,
            $user
        );

        $this->em->persist($log);
        $this->em->flush();
    }
}
