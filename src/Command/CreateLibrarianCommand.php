<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-librarian',
    description: 'Create a librarian user',
)]
class CreateLibrarianCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Librarian email address')
            ->addArgument('password', InputArgument::OPTIONAL, 'Librarian password')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force creation even if user already exists')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer l'email
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Email address: ');
            $question->setValidator(function ($value) {
                if (empty($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Please enter a valid email address');
                }
                return $value;
            });
            $email = $io->askQuestion($question);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser && !$input->getOption('force')) {
            $io->error(sprintf('User with email "%s" already exists. Use --force to update.', $email));
            return Command::FAILURE;
        }

        // Récupérer le mot de passe
        $password = $input->getArgument('password');
        if (!$password) {
            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $question->setValidator(function ($value) {
                if (empty($value) || strlen($value) < 6) {
                    throw new \Exception('Password must be at least 6 characters long');
                }
                return $value;
            });
            $password = $io->askQuestion($question);
        }

        // Créer ou mettre à jour l'utilisateur
        if ($existingUser) {
            $user = $existingUser;
            $io->note('Updating existing user...');
        } else {
            $user = new User();
            $user->setEmail($email);
        }

        $user->setRoles([User::ROLE_LIBRARIAN]);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Sauvegarder
        if (!$existingUser) {
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        $io->success([
            sprintf('Librarian user "%s" created successfully!', $email)
        ]);

        return Command::SUCCESS;
    }
}