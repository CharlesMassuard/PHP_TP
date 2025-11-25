<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:clear-logs',
    description: 'Supprime les logs de plus de 30 jours',
)]
class ClearLogsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Supprime les logs de plus de 30 jours');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateLimit = new \DateTimeImmutable('-30 days');

        $qb = $this->em->createQueryBuilder()
            ->delete('App\Entity\AuditLog', 'a')
            ->where('a.date < :date')
            ->setParameter('date', $dateLimit);

        $qb->getQuery()->execute();

        $output->writeln('Logs d’audit nettoyés.');
        return Command::SUCCESS;
    }
}
