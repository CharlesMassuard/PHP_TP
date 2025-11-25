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
    name: 'app:envoi-rappels',
    description: 'Envoie des rappels pour les emprunts',
)]
class EnvoiRappelsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Envoie des rappels pour les emprunts arrivant à échéance dans 3 jours');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->rappel7Jours($input, $output);
        $this->rappelAjd($input, $output);
        $this->rappel3Jours($input, $output);
        return Command::SUCCESS;
    }

    private function rappel7Jours(InputInterface $input, OutputInterface $output): void
    {
        $dateLimit = new \DateTimeImmutable('+7 days');
        $emprunts = $this->em->getRepository(\App\Entity\Emprunt::class)->findEmpruntsExpirantDate($dateLimit);
        foreach ($emprunts as $emprunt) {
            $output->writeln(sprintf('+7Jours : Rappel envoyé pour l\'emprunt ID %d, échéance le %s', $emprunt->getId(), $emprunt->getDateRetour()->format('Y-m-d')));
        }
    }

    private function rappelAjd(InputInterface $input, OutputInterface $output): void
    {
        $dateLimit = new \DateTimeImmutable();
        $emprunts = $this->em->getRepository(\App\Entity\Emprunt::class)->findEmpruntsExpirantDate($dateLimit);
        foreach ($emprunts as $emprunt) {
            $output->writeln(sprintf('Aujourd\'hui : Rappel envoyé pour l\'emprunt ID %d, échéance le %s', $emprunt->getId(), $emprunt->getDateRetour()->format('Y-m-d')));
        }
    }

    private function rappel3Jours(InputInterface $input, OutputInterface $output): void
    {
        $dateLimit = new \DateTimeImmutable('-3 days');
        $emprunts = $this->em->getRepository(\App\Entity\Emprunt::class)->findEmpruntsExpirantDate($dateLimit);
        foreach ($emprunts as $emprunt) {
            $output->writeln(sprintf('-3Jours : Rappel envoyé pour l\'emprunt ID %d, échéance le %s', $emprunt->getId(), $emprunt->getDateRetour()->format('Y-m-d')));
        }
    }
}
