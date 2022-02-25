<?php

namespace App\Command;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Item;
use App\Entity\Job;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:import:user',
    description: 'Add a short description for your command',
)]
class UserCommand extends Command
{
    public ParameterBagInterface $parameterBag;
    public EntityManagerInterface $em;
    public UserPasswordHasherInterface $passwordHasher;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $emails = ['thibault.guillemin@gmail.com'];

        foreach ($emails as $email) {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $user = new User();
            }

            $plaintextPassword = 'Chapier1';

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setEmail($email)->setFamilyName('test')->setFirstName('test1')->setPassword($hashedPassword);
            $this->em->persist($user);
        }

        $this->em->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
