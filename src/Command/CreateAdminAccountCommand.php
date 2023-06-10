<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin-account',
    description: 'Creates an admin account.',
)]
class CreateAdminAccountCommand extends Command
{
    /**
     * @var App\Service\UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     * Command name and parameters
     */
    protected function configure()
    {
        $this
                ->setDescription('Create the first admin account.')
                ->addArgument('admin-username', InputArgument::REQUIRED, 'Admin username')
                ->addArgument('admin-password', InputArgument::REQUIRED, 'Admin password')
        ;
    }

    /**
     * Actions executed when running the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Admin account creation started...');

        $this->userService->createFirstAdminAccount(
                $input->getArgument('admin-username'),
                $input->getArgument('admin-password')
        );

        $io->success('User successfully generated!');

        return Command::SUCCESS;
    }
}
